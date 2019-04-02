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
        $data = array(
            'question' => $question,
            'lesson' => $lesson
        );
        return view('lessons.show')->with($data);
    }

    public function create(Request $request, Track $track) {
        $titles = Title::all();
        $data = array(
            'track' => $track,
            'titles' => $titles
        );
        return view('lessons.create')->with($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'track_id' => 'required'
        ]);

        $lesson = new Lesson;
        $lesson->track_id = $request->track_id;
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
        $data = array(
            'lesson' => $lesson,
            'titles' => $titles
        );
        return view('lessons.edit')->with($data);
    }

    public function editquestions(Lesson $lesson) {
        $questions = $lesson->questions->sortBy('order');
        $data = array(
            'lesson' => $lesson,
            'questions' => $questions
        );
        return view('lessons.editquestions')->with($data);
    }

    public function update(Request $request, Lesson $lesson) {
        $this->validate($request, [
            'name' => 'required',
            'new_audio.*' => 'file|mimetypes:audio/mpeg',
            'new_html.*' => 'string',
            'html.*' => 'string',
            'new_vimeo.*' => 'integer',
            'vimeo.*' => 'integer'
        ],
        ['name.required' => __('Du måste ange ett namn på lektionen!'),
        'new_audio.*.mimetypes' => __('Din ljudfil måste vara i mp3-format!'),
        'new_audio.*.file' => __('Du måste välja en fil add ladda upp!'),
        'new_html.*.string' => __('Du måste skriva någon text i textrutan!'),
        'html.*.string' => __('Du måste skriva någon text i textrutan!'),
        'new_vimeo.*.integer' => __('Ett giltigt Vimeo-id har bara siffror!'),
        'vimeo.*.integer' => __('Ett giltigt Vimeo-id har bara siffror!')]);

        $currentLocale = \App::getLocale();

        //Store this in a local variable. We'll have to replace all the temporary id's in it for real ones before we do the ordering
        $content_order = $request->content_order;

        //Loop through all changed html contents
        if($request->html) {
            foreach($request->html as $html_id => $html_text) {
                $content = Content::find($html_id);
                $content->translateOrNew($currentLocale)->text = $html_text;
                $content->save();
            }
        }

        //Loop through all added html contents
        if($request->new_html) {
            foreach($request->new_html as $temp_key => $new_html) {
                logger("Ny html-content med följande innehåll: ".$new_html);
                $content = new Content();
                $content->type = 'html';
                $content->translateOrNew($currentLocale)->text = $new_html;
                $content->lesson_id = $lesson->id;
                $content->save();
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
            }
        }

        //Loop through all deleted html contents
        if($request->remove_html) {
            foreach($request->remove_html as $remove_html_id => $remove_html) {
                Content::destroy($remove_html_id);
            }
        }

        //Loop through all changed vimeo contents
        if($request->vimeo) {
            foreach($request->vimeo as $vimeo_id => $vimeo_text) {
                $content = Content::find($vimeo_id);
                $content->content = $vimeo_text;
                $content->save();
            }
        }

        //Loop through all added vimeo contents
        if($request->new_vimeo) {
            foreach($request->new_vimeo as $temp_key => $new_vimeo) {
                logger("Ny Vimeo-content med följande innehåll: ".$new_vimeo);
                $content = new Content();
                $content->type = 'vimeo';
                $content->content = $new_vimeo;
                $content->lesson_id = $lesson->id;
                $content->save();
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
            }
        }

        //Loop through all deleted vimeo contents
        if($request->remove_vimeo) {
            foreach($request->remove_vimeo as $remove_vimeo_id => $remove_vimeo) {
                Content::destroy($remove_vimeo_id);
            }
        }


        //Loop through all added audio contents
        if($request->new_audio) {
            foreach($request->new_audio as $temp_key => $new_audio) {
                $filename = $new_audio->getClientOriginalName();
                $new_audio->storeAs('public/pods', $filename); //TODO: Måste kolla varför denna bara skapar helt tomma filer i labmiljön, funkar bra i dev
                $content = new Content();
                $content->type = 'audio';
                $content->content = $filename;
                $content->lesson_id = $lesson->id;
                $content->save();
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
            }
        }

        //Loop through all deleted audio contents
        if($request->remove_audio) {
            foreach($request->remove_audio as $remove_audio_id => $remove_audio) {
                $content = Content::find($remove_audio_id);
                Storage::delete("public/pods/".$content->content);
                Content::destroy($remove_audio_id);
            }
        }

        //Fix sort order of all contents
        $i = 0;
        foreach(explode(",", $content_order) as $order) {
            preg_match('#\[(.*?)\]#', $order, $match); //Exctract the id, which is between []
            $id = $match[1];
            $content = Content::find($id);
            if($content) {
                $content->order = $i++;
                $content->save();
            }
        }

        $lesson->translateOrNew($currentLocale)->name = $request->name;
        $lesson->active = $request->active;
        $lesson->limited_by_title = $request->limited_by_title;
        $lesson->save();

        $lesson->titles()->sync($request->titles);

        return redirect('/lessons/'.$lesson->id)->with('success', __('Ändringar sparade'));
    }
}
