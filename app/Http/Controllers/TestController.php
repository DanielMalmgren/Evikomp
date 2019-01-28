<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lesson;
use App\Question;
use App\ResponseOption;
use App\TestResponse;
use App\TestSession;
use App\LessonResult;
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

    public function store(StoreTestResponse $request) {
        //$test_response = TestResponse::find($request->input('test_response_id'));
        $test_response = TestResponse::find($request->session()->get('test_response_id'));
        //$testsession = TestSession::find($request->input('testsession_id'));
        //$question_id = $request->input('question_id');
        //$question = Question::find($request->input('question_id'));
        $question = $test_response->question;
        $test_session = $test_response->test_session;
        $lesson = $test_session->lesson;

        //$test_session->completed_questions++;
        $test_session->save();

        $nextquestion = Question::where([['lesson_id', '=', $lesson->id],['order', '>', $question->order]])->first();
        if($nextquestion) {
            //return redirect('/test/'.$lesson->id.'/'.$nextquestion->id);
            $request->session()->forget('test_response_id'); //Rensa denna så det skapas en ny när vi kommer till QuestionController@show
            return redirect('/test/question/'.$nextquestion->id.'?testsession_id='.$test_session->id);
        } else {
            $lesson_result = LessonResult::updateOrCreate(
                ['user_id' => $test_session->user_id, 'lesson_id' => $test_session->lesson_id]
            );
            if($test_session->percent() > $lesson_result->personal_best_percent) {
                $lesson_result->personal_best_percent = $test_session->percent();
                $lesson_result->save();
            }

            return redirect('/test/result/'.$test_session->id);
        }
    }
}
