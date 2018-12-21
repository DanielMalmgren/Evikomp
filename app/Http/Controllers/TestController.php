<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lesson;
use App\Question;
use App\ResponseOption;
use App\TestSession;
use App\Http\Requests\StoreTestResponse;

class TestController extends Controller
{
    public function show($lesson_id) {

        $questions = Question::where('lesson_id', $lesson_id);

        $test_session = new TestSession;
        $test_session->lesson_id = $lesson_id;
        $test_session->user_id = Auth::user()->id;
        //$test_session->number_of_questions = $questions->count();
        $test_session->save();

        logger('Testsessions-ID: '.$test_session->id);

        $question = $questions->orderBy('order')->first();
        return redirect('/test/question/'.$question->id.'?testsession_id='.$test_session->id);
    }
}
