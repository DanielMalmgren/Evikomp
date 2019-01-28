<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\ResponseOption;
use App\TestSession;
use App\TestResponse;
use App\LessonResult;
use App\Http\Requests\StoreTestResponse;

class QuestionController extends Controller
{
    public function show(Request $request, Question $question) {
        logger('Session ID: '.$request->query('testsession_id'));
        $test_session = TestSession::find($request->query('testsession_id'));

        $test_response = TestResponse::firstOrCreate(
            ['test_session_id' => $test_session->id,
            'question_id' => $question->id]
        );

        $request->session()->put('test_response_id', $test_response->id);

        $data = array(
            'question' => $question,
            'test_session' => $test_session,
            'test_response' => $test_response
        );
        return view('questions.show')->with($data);
    }

    public function edit(Question $question) {
        $data = array(
            'question' => $question
        );
        return view('questions.edit')->with($data);
    }

    public function update(Request $request, Question $question) {
        $this->validate($request, [
            'text' => 'required',
            'correctAnswers' => 'required'
        ]);

        $currentLocale = \App::getLocale();
        $question->translate($currentLocale)->text = $request->text;
        $question->correctAnswers = $request->correctAnswers;
        $question->save();

        //logger('Alternativ:');
        //logger(print_r($request->response_option_text, true));
        //logger('Alternativ rätt:');
        //logger(print_r($request->response_option_correct, true));

        foreach($request->response_option_text as $response_option_id => $response_option_text) {
            $response_option = ResponseOption::find($response_option_id);
            $response_option->text = $response_option_text;
            $response_option->isCorrectAnswer = in_array($response_option_id, $request->response_option_correct);
            $response_option->save();
        }

        return redirect('/lessons/'.$question->lesson->id.'/edit')->with('success', 'Ändringar sparade');
    }
}
