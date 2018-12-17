<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Question;

class LessonController extends Controller
{
    public function show($lesson_id) {
        $lesson = Lesson::where('id', $lesson_id)->first();
        $question = Question::where('lesson_id', $lesson_id)->first();
        $data = array(
            'question' => $question,
            'lesson' => $lesson
        );
        return view('pages.lesson')->with($data);
    }
}
