<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Track;
use App\Question;
use App\Title;
use App\LessonResult;
use App\Content;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function show(Lesson $lesson) {
        $question = Question::where('lesson_id', $lesson->id)->first();
        $lesson->times_started++;
        $lesson->save();
        $data = [
            'question' => $question,
            'lesson' => $lesson,
        ];
        return view('lessons.show')->with($data);
    }

    public function replicate(Lesson $lesson) {
        $newLesson = $lesson->replicateWithTranslations();
        $newLesson->times_started=0;
        $newLesson->times_test_started=0;
        $newLesson->times_finished=0;
        $newLesson->active=0;
        $newLesson->push();

        foreach($lesson->contents as $content) {
            $newContent = $content->replicateWithTranslations();
            $newContent->lesson_id=$newLesson->id;
            $newContent->push();
            if($newContent->type=='file' || $newContent->type=='image' || $newContent->type=='office' || $newContent->type=='audio') {
                Storage::copy($content->filepath().$content->filename(), $newContent->filepath().$newContent->filename());
            }
        }

        return redirect('/lessons/'.$newLesson->id)->with('success', __('Lektionen har kopierats'));
    }

    public function create(Track $track) {
        $titles = Title::all();
        $data = [
            'track' => $track,
            'titles' => $titles,
        ];
        return view('lessons.create')->with($data);
    }

    public function store(Request $request) {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
            'track' => 'required',
        ],
        ['name.required' => __('Du måste ange ett namn på lektionen!')]);

        $currentLocale = \App::getLocale();

        $track = Track::find($request->track);

        $lesson = new Lesson();
        $lesson->track_id = $request->track;
        $lesson->order = $track->lessons->max('order')+1;
        $lesson->translateOrNew($currentLocale)->name = $request->name;
        $lesson->save();

        return $this->update($request, $lesson);
    }

    public function vote(Request $request, Lesson $lesson) {
        $lesson_result = LessonResult::where([['user_id', '=', Auth::user()->id],['lesson_id', '=', $lesson->id]])->first();
        $lesson_result->rating = $request->vote;
        $lesson_result->save();
    }

    public function reorder(Request $request) {
        parse_str($request->data, $data);
        $ids = $data['id'];

        foreach($ids as $order => $id){
            $lesson = Lesson::findOrFail($id);
            $lesson->order = $order+1;
            $lesson->save();
        }
    }

    public function edit(Lesson $lesson) {
        $titles = Title::all();
        $data = [
            'lesson' => $lesson,
            'titles' => $titles,
            'tracks' => Track::all(),
        ];
        return view('lessons.edit')->with($data);
    }

    public function editquestions(Lesson $lesson) {
        $questions = $lesson->questions->sortBy('order');
        $data = [
            'lesson' => $lesson,
            'questions' => $questions,
        ];
        return view('lessons.editquestions')->with($data);
    }

    public function finish(Lesson $lesson) {
        LessonResult::updateOrCreate(
            ['user_id' => Auth::user()->id, 'lesson_id' => $lesson->id]
        );
        return redirect('/');
    }

    public function update(Request $request, Lesson $lesson) {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
            'track' => 'required',
            'new_audio.*' => 'file|mimetypes:audio/mpeg|max:20000',
            'new_office.*' => 'file|mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation|max:10000',
            'new_image.*' => 'file|image|max:20000',
            'new_file.*' => 'file|max:20000',
            'new_html.*' => 'string',
            'html.*' => 'string',
            'new_vimeo.*' => 'integer',
            'vimeo.*' => 'integer',
        ],
        [
            'name.required' => __('Du måste ange ett namn på lektionen!'),
            'new_audio.*.mimetypes' => __('Din ljudfil måste vara i mp3-format!'),
            'new_office.*.mimetypes' => __('Din file måste vara antingen ett Word-dokument, en Excel-fil eller en Powerpoint-presentation!'),
            'new_image.*.image' => __('Felaktigt bildformat!'),
            'new_audio.*.file' => __('Du måste välja en fil att ladda upp!'),
            'new_office.*.file' => __('Du måste välja en fil att ladda upp!'),
            'new_image.*.file' => __('Du måste välja en fil att ladda upp!'),
            'new_file.*.file' => __('Du måste välja en fil att ladda upp!'),
            'new_audio.*.max' => __('Din fil är för stor! Max-storleken är 20MB!'),
            'new_office.*.max' => __('Din fil är för stor! Max-storleken är 10MB!'),
            'new_image.*.max' => __('Din fil är för stor! Max-storleken är 20MB!'),
            'new_file.*.max' => __('Din fil är för stor! Max-storleken är 20MB!'),
            'new_html.*.string' => __('Du måste skriva någon text i textrutan!'),
            'html.*.string' => __('Du måste skriva någon text i textrutan!'),
            'new_vimeo.*.integer' => __('Ett giltigt Vimeo-id har bara siffror!'),
            'vimeo.*.integer' => __('Ett giltigt Vimeo-id har bara siffror!'),
        ]);

        $currentLocale = \App::getLocale();
        $user = Auth::user();
        logger("Lesson ".$lesson->id." is being edited by ".$user->name);

        //Store this in a local variable. We'll have to replace all the temporary id's in it for real ones before we do the ordering
        $content_order = $request->content_order;

        //Loop through all changed html contents
        if($request->html) {
            foreach($request->html as $html_id => $html_text) {
                $content = Content::find($html_id);
                $content->translateOrNew($currentLocale)->text = $content->add_target_to_links($html_text);
                $content->save();
                logger("HTML content ".$html_id." is being changed");
            }
        }

        //Loop through all added html contents
        if($request->new_html) {
            foreach($request->new_html as $temp_key => $new_html) {
                $content = new Content('html', $lesson->id, null, $new_html);
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("HTML content ".$content->id." is being added");
            }
        }

        //Loop through all changed vimeo contents
        if($request->vimeo) {
            foreach($request->vimeo as $vimeo_id => $vimeo_text) {
                $content = Content::find($vimeo_id);
                $content->content = $vimeo_text;
                $content->save();
                logger("Vimeo content ".$vimeo_id." is being changed");
            }
        }

        //Loop through all added vimeo contents
        if($request->new_vimeo) {
            foreach($request->new_vimeo as $temp_key => $new_vimeo) {
                $content = new Content('vimeo', $lesson->id, $new_vimeo);
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("Vimeo content ".$content->id." is being added");
            }
        }

        //Loop through all added audio contents
        if($request->new_audio) {
            foreach($request->new_audio as $temp_key => $new_audio) {
                $content = new Content('audio', $lesson->id, $new_audio->getClientOriginalName());
                $new_audio->storeAs($content->filepath(true), $content->filename());
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("Audio content ".$content->id." is being added");
            }
        }

        //Loop through all added office contents
        if($request->new_office) {
            foreach($request->new_office as $temp_key => $new_office) {
                $content = new Content('office', $lesson->id, $new_office->getClientOriginalName());
                $new_office->storeAs($content->filepath(true), $content->filename());
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("Office content ".$content->id." is being added");
            }
        }

        //Loop through all added image files
        if($request->new_image) {
            foreach($request->new_image as $temp_key => $new_image) {
                $content = new Content('image', $lesson->id, $new_image->getClientOriginalName());
                $new_image->storeAs($content->filepath(true), $content->filename());
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("Image content ".$content->id." is being added");
            }
        }

        //Loop through all added file contents
        if($request->new_file) {
            foreach($request->new_file as $temp_key => $new_file) {
                $content = new Content('file', $lesson->id, $new_file->getClientOriginalName());
                $new_file->storeAs($content->filepath(true), $content->filename());
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("File content ".$content->id." is being added");
            }
        }

        //Loop through all changed content files
        if($request->replace_file) {
            foreach($request->replace_file as $content_id => $new_file) {
                logger("Image content ".$content_id." is being changed to ".$new_file);
                $content = Content::find($content_id);
                logger("Deleting ".$content->filepath().$content->filename()." from disk");
                Storage::delete($content->filepath().$content->filename());
                $content->translateOrNew($currentLocale)->text = $new_file->getClientOriginalName();
                $content->save();
                $new_file->storeAs($content->filepath(true), $content->filename());
            }
        }

        //Loop through all deleted contents
        if($request->remove_content) {
            foreach(array_keys($request->remove_content) as $content_id) {
                logger("Deleting content ".$content_id);
                Content::destroy($content_id);
            }
        }

        //Fix sort order of all contents
        $i = 0;
        if(strlen($content_order) > 0) {
            foreach(explode(",", $content_order) as $order) {
                preg_match('#\[(.*?)\]#', $order, $match); //Exctract the id, which is between []
                $id = $match[1];
                $content = Content::find($id);
                if($content) {
                    $content->order = $i;
                    $content->save();
                    $i++;
                }
            }
        }

        $lesson->translateOrNew($currentLocale)->name = $request->name;
        $lesson->active = $request->active;
        $lesson->track_id = $request->track;
        $lesson->limited_by_title = $request->limited_by_title;
        $lesson->save();

        $lesson->titles()->sync($request->titles);

        return redirect('/lessons/'.$lesson->id)->with('success', __('Ändringar sparade'));
    }

    public function destroy(Lesson $lesson) {
        $user = Auth::user();
        logger("Lesson ".$lesson->id." is being removed by ".$user->name);
        foreach($lesson->contents as $content) {
            $content->delete();
        }
        $lesson->delete();
    }
}
