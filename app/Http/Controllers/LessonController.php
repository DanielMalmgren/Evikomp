<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Question;
use App\Title;
use App\LessonResult;

class LessonController extends Controller
{
    public function show(Lesson $lesson) {
        $question = Question::where('lesson_id', $lesson->id)->first();
        $data = array(
            'question' => $question,
            'lesson' => $lesson
        );
        return view('lessons.show')->with($data);
    }

    public function vote(Request $request, Lesson $lesson) {
        $lesson_result = LessonResult::where([['user_id', '=', $request->user_id],['lesson_id', '=', $lesson->id]])->first();
        $lesson_result->rating = $request->vote;
        $lesson_result->save();
    }

    public function edit(Lesson $lesson) {
        $titles = Title::all();
        $data = array(
            'lesson' => $lesson,
            'titles' => $titles
        );
        return view('lessons.edit')->with($data);
    }

    public function update(Request $request, Lesson $lesson) {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $currentLocale = \App::getLocale();
        $lesson->translate($currentLocale)->name = $request->name;
        $lesson->active = $request->active;
        $lesson->limited_by_title = $request->limited_by_title;
        $lesson->translate($currentLocale)->description = $request->description;
        $lesson->save();

        $lesson->titles()->sync($request->titles);

        return redirect('/lessons/'.$lesson->id)->with('success', 'Ändringar sparade');
    }
}
