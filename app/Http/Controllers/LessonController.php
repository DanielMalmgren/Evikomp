<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Track;
use App\Question;
use App\Title;
use App\LessonResult;
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

    public function edit(Lesson $lesson) {
        $titles = Title::all();
        $questions = $lesson->questions->sortBy('order');
        $data = array(
            'lesson' => $lesson,
            'titles' => $titles,
            'questions' => $questions
        );
        return view('lessons.edit')->with($data);
    }

    public function update(Request $request, Lesson $lesson) {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $currentLocale = \App::getLocale();
        $lesson->translateOrNew($currentLocale)->name = $request->name;
        $lesson->active = $request->active;
        $lesson->video_id = $request->video_id;
        $lesson->limited_by_title = $request->limited_by_title;
        $lesson->translateOrNew($currentLocale)->description = $request->description;
        $lesson->save();

        $lesson->titles()->sync($request->titles);

        return redirect('/lessons/'.$lesson->id)->with('success', 'Ändringar sparade');
    }
}
