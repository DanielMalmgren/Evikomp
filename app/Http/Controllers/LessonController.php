<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Question;

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

    public function edit(Lesson $lesson) {
        $data = array(
            'lesson' => $lesson
        );
        return view('lessons.edit')->with($data);
    }

    public function update(Request $request, Lesson $lesson) {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $currentLocale = \App::getLocale();
        $lesson->translate($currentLocale)->name = $request->name;
        $lesson->translate($currentLocale)->description = $request->description;
        $lesson->save();

        return redirect('/lessons/'.$lesson->id)->with('success', 'Ändringar sparade');
    }
}
