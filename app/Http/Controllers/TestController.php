<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Question;
use App\ResponseOption;

class TestController extends Controller
{
    public function show($lesson_id, $order = null) {
        $lesson = Lesson::where('id', $lesson_id)->first();
        //Om $order är null, börja med första frågan
        if(!$order) {
            $question = Question::where('lesson_id', $lesson_id)->first();
        } else {
            $question = Question::where([['lesson_id', '=', $lesson_id],['order', '>=', $order]])->first();
            if(!$question) {
                //Om det inte finns någon fråga med $order är testet klart, dirigera om till sidan med resultat
                return redirect('/testresult/'.$lesson_id);
            }
        }

        $responseoptions = ResponseOption::where('question_id', $question->id)->get();

        $data = array(
            'question' => $question,
            'lesson' => $lesson,
            'responseoptions' => $responseoptions
        );
        return view('pages.question')->with($data);
    }
}
