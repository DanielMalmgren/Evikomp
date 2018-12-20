<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\ResponseOption;
use App\TestSession;
use App\TestResponse;
use App\Http\Requests\StoreTestResponse;

class QuestionController extends Controller
{
    public function show(Request $request, $question_id) {
        logger('Session ID: '.$request->query('testsession_id'));
        //$testsession = TestSession::where('id', $request->query('testsession_id'));
        $test_session = TestSession::find($request->query('testsession_id'));

        $question = Question::find($question_id);

        $test_response = TestResponse::find($request->session()->get('test_response_id'));
        if(!$test_response || $test_response->test_session_id != $test_session->id) {
            $test_response = new TestResponse();
            $test_response->test_session_id = $test_session->id;
            $test_response->question_id = $question->id;
            $test_response->save();
            $request->session()->put('test_response_id', $test_response->id);
        }

        //session(['test_response_id' => $test_response->id]);

        //$lesson = $question->lesson;

        /*$lesson = Lesson::where('id', $lesson_id)->first();
        //Om $question_id är null, börja med första frågan
        if(!$question_id) {
            $question = Question::where('lesson_id', $lesson_id)->first();
        } else {
            $question = Question::where('id', $question_id)->first();
        }*/

        $response_options = ResponseOption::where('question_id', $question->id)->get();

        $data = array(
            'question' => $question,
        //    'lesson' => $lesson,
            'response_options' => $response_options,
            'test_session' => $test_session,
            'test_response' => $test_response
        );
        return view('pages.question')->with($data);
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

        $test_session->completed_questions++;
        $test_session->save();

        $nextquestion = Question::where([['lesson_id', '=', $lesson->id],['order', '>', $question->order]])->first();
        if($nextquestion) {
            //return redirect('/test/'.$lesson->id.'/'.$nextquestion->id);
            $request->session()->forget('test_response_id'); //Rensa denna så det skapas en ny när vi kommer till QuestionController@show
            return redirect('/test/question/'.$nextquestion->id.'?testsession_id='.$test_session->id);
        } else {
            return redirect('/test/result/'.$test_session->id);
        }
    }
}
