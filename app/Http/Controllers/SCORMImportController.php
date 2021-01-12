<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Track;
use App\Content;
use App\Question;
use App\ResponseOption;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SCORMImportController extends Controller
{

    private int $order;

    public function create(Track $track): View {
        $data = [
            'track' => $track,
        ];
        return view('scormimport.create')->with($data);
    }

    public function store(Request $request): RedirectResponse {
        usleep(50000);
        $this->validate($request, [
            'track' => 'required',
            'scormfile' => 'required',
        ],
        [
            'scormfile.required' => __('Saknar fil!'),
        ]);

        $path = $request->scormfile->store('public/app/temp');

        $dir = pathinfo($path, PATHINFO_FILENAME);

        $zip = new \ZipArchive;
        $zip->open('../storage/app/'.$path);
        $zip->extractTo('../storage/app/temp/'.$dir);
        $zip->close();

        Storage::delete($path);

        $manifest = simplexml_load_file('../storage/app/temp/'.$dir.'/imsmanifest.xml');

        $title = $manifest->organizations->organization->title;

        logger("Titel: ".$title);

        $currentLocale = \App::getLocale();

        $track = Track::find($request->track);

        $lesson = new Lesson();
        $lesson->track_id = $request->track;
        $lesson->order = $track->lessons->max('order')+1;
        $lesson->translateOrNew($currentLocale)->name = $title;
        $lesson->save();

        $this->order = 1;

        foreach($manifest->resources->resource->file as $file) {
            logger("Handling file ".$file['href']);
            if(strpos($file['href'], 'pages/') === 0) {
                $filecontents = file_get_contents('../storage/app/temp/'.$dir.'/'.$file['href']);
                $this->handle_elucidat_page($filecontents, $lesson);
                //logger(substr($html, 0, 100));
            }
        }

        //TODO: Also delete the extracted directory

        return redirect('/lessons/'.$lesson->id)->with('success', __('Importen lyckades!'));
    }

    private function handle_elucidat_page(string $filecontents, Lesson $lesson) {
        $html = $this->fix_elucidat_madness($filecontents);

        $currentLocale = \App::getLocale();

        //First, find the title of this page
        $pagetitlestartpos = strpos($html, 'data-role="page.name">')+22;
        $pagetitlelength = strpos($html, '</h1>', $pagetitlestartpos)-$pagetitlestartpos;
        $pagetitle = substr($html, $pagetitlestartpos, $pagetitlelength);

        $content = new Content('pagebreak', $lesson->id, null, $pagetitle, $this->order);
        $content->save();
        $this->order++;

        //Now go through the file and find all relevant lesson elements
        $startpos = 0;
        while(strpos($html, 'class="media', $startpos) !== false) {
            $mediatagstartpos = strpos($html, 'class="media', $startpos)+13;
            $mediataglength = strpos($html, '"', $mediatagstartpos)-$mediatagstartpos;
            $mediatype = substr($html, $mediatagstartpos, $mediataglength);
            //logger("Found media of type ".$mediatype." between ".$mediatagstartpos." and ".$mediataglength);

            $mediacontentstartpos = strpos($html, '>', $mediatagstartpos)+1;
            $mediacontentlength = strpos($html, '</div>', $mediacontentstartpos)-$mediacontentstartpos;
            $mediacontent = substr($html, $mediacontentstartpos, $mediacontentlength);
            //logger("Media content: ".$mediacontent);

            switch ($mediatype) {
                case "htmlText":
                    if(strpos($mediacontent, '<p>Sample text') === false) {
                        $mediacontent = strip_tags($mediacontent, '<p><h3><h4><strong><em><br><ul><li>');
                        $content = new Content('html', $lesson->id, null, $mediacontent, $this->order);
                        $this->order++;
                    }
                    break;
                case "video":
                    if(strpos($mediacontent, 'youtube') !== false) {
                        $id = $this->extract_youtube_id($mediacontent);
                        $content = new Content('youtube', $lesson->id, $id, null, $this->order);
                        $this->order++;
                    }
                    break;
            }

            $startpos = $mediatagstartpos+1;
        }

        //Go through the file again, now in search for lesson questions
        $startpos = 0;
        while(strpos($html, 'class="mod questionnaire', $startpos) !== false) {
            $formstart = strpos($html, 'class="mod questionnaire', $startpos);
            $formlength = strpos($html, '</form>', $formstart)-$formstart;
            $form = substr($html, $formstart, $formlength);
            $qstart = strpos($form, 'data-role="question">')+21;
            $qlength = strpos($form, '</div>', $qstart)-$qstart;
            $questiontext = strip_tags(substr($form, $qstart, $qlength));
            logger("Found following question: ".$questiontext);

            $question = new Question();
            $question->lesson_id = $lesson->id;
            $question->order = $lesson->questions->max('order')+1;
            $question->translateOrNew($currentLocale)->text = $questiontext;
            $question->save();
            logger("It started at pos ".$formstart." and has length ".$formlength.", I gave it ID ".$question->id);

            //Go through all answer alternatives
            $formstartpos = 0;
            $correctAnswers = 0;
            while(strpos($form, 'data-role="answer"', $formstartpos) !== false) {
                $altstart = strpos($form, 'data-role="answer"', $formstartpos);

                $textstart = strpos($form, 'class="text">', $altstart)+13;
                $textlength = strpos($form, '</label>', $textstart)-$textstart;
                $alttext = strip_tags(substr($form, $textstart, $textlength));

                if($alttext != 'Type your answer here') {
                    if(strpos($form, 'data-status="correct"', $formstartpos) < $textstart) {
                        $altcorrect = true;
                        $correctAnswers++;
                    } else {
                        $altcorrect = false;
                    }

                    $response_option = new ResponseOption();
                    $response_option->text = $alttext;
                    $response_option->isCorrectAnswer = $altcorrect;
                    $response_option->question_id = $question->id;
                    $response_option->save();
                }

                $formstartpos = $altstart+1;
            }

            $question->correctAnswers = $correctAnswers;
            $question->save();

            $startpos = $formstart+1;
        }

    }

    private function extract_youtube_id(string $mediacontent) {
        $urlstartpos = strpos($mediacontent, 'https://');
        $url = substr($mediacontent, $urlstartpos, strpos($mediacontent, '"', $urlstartpos)-$urlstartpos);

        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
        
        return $matches[1];
    }

    //SCORM files from Elucidat are complete madness. This function tries to salvage what can be saved from them
    private function fix_elucidat_madness(string $filecontents) {
        $from = strpos($filecontents, '"lmth":')+8;
        $to = strpos($filecontents, '"', $from)-$from;
        $filecontents = substr($filecontents, $from, $to);

        //Replace all unicode coded characters (\u0022) with real ones
        $filecontents = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $filecontents);

        //Fix escaped characters (\/)
        $filecontents = stripcslashes($filecontents);

        //Reverse the entire text
        $filecontents = strrev($filecontents);

        //Decode html encoded characters (&auml;)
        $filecontents = html_entity_decode($filecontents);

        return $filecontents;
    }
}
