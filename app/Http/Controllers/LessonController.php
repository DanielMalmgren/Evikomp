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
            'name' => 'required'
        ]);

        $currentLocale = \App::getLocale();

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
            foreach($request->new_html as $new_html) {
                logger("Ny html-content med följande innehåll: ".$new_html);
                $content = new Content();
                $content->type = 'html';
                $content->translateOrNew($currentLocale)->text = $new_html;
                $content->lesson_id = $lesson->id;
                $content->save();
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
            foreach($request->new_vimeo as $new_vimeo) {
                logger("Ny Vimeo-content med följande innehåll: ".$new_vimeo);
                $content = new Content();
                $content->type = 'vimeo';
                $content->content = $new_vimeo;
                $content->lesson_id = $lesson->id;
                $content->save();
            }
        }

        //Loop through all deleted vimeo contents
        if($request->remove_vimeo) {
            foreach($request->remove_vimeo as $remove_vimeo_id => $remove_vimeo) {
                Content::destroy($remove_vimeo_id);
            }
        }

        //Fix sort order of all contents
        $i = 0;
        foreach(explode(",", $request->content_order) as $order) {
            preg_match('#\[(.*?)\]#', $order, $match); //Exctract the id, which is between []
            $id = $match[1];
            $content = Content::find($id);
            $content->order = $i++;
            $content->save();
        }

        $lesson->translateOrNew($currentLocale)->name = $request->name;
        $lesson->active = $request->active;
        $lesson->limited_by_title = $request->limited_by_title;
        $lesson->save();

        $lesson->titles()->sync($request->titles);

        return redirect('/lessons/'.$lesson->id)->with('success', 'Ändringar sparade');
    }
}
