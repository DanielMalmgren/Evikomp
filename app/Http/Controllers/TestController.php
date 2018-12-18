<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Question;
use App\ResponseOption;
use App\Http\Requests\StoreTestResponse;

class TestController extends Controller
{
    public function show($lesson_id, $question_id = null) {
        $lesson = Lesson::where('id', $lesson_id)->first();
        //Om $question_id är null, börja med första frågan
        if(!$question_id) {
            $question = Question::where('lesson_id', $lesson_id)->first();
        } else {
            $question = Question::where('id', $question_id)->first();
        }

        $responseoptions = ResponseOption::where('question_id', $question->id)->get();

        $data = array(
            'question' => $question,
            'lesson' => $lesson,
            'responseoptions' => $responseoptions
        );
        return view('pages.question')->with($data);
    }

    public function store(StoreTestResponse $request) {
        $question_id = $request->input('question_id');
        $question = Question::where('id', $question_id)->first();
        $lesson = $question->lesson;

        //TODO
        //Lägg till kod för att spara någonstans i databasen om man har svarat rätt eller fel
        //...fast hit kommer man ju aldrig om man inte passerar validation. Skit då...
        //Kan nog kanske lösas med withValidator, se https://laravel.com/docs/5.7/validation

        $nextquestion = Question::where([['lesson_id', '=', $lesson->id],['order', '>', $question->order]])->first();
        if($nextquestion) {
            return redirect('/test/'.$lesson->id.'/'.$nextquestion->id);
        } else {
            return redirect('/testresult/'.$lesson->id);
        }
    }
}
