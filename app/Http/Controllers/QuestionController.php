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

    public function create(Request $request) {
        $lesson_id = $request->input('lesson_id');
        $data = array(
            'lesson_id' => $lesson_id
        );
        return view('questions.create')->with($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'text' => 'required',
            'correctAnswers' => 'required|integer',
            'lesson_id' => 'required'
        ]);

        $question = new Question;
        $question->lesson_id = $request->lesson_id;
        $question->order = 100;
        $question->save();

        $this->update($request, $question);
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
            'correctAnswers' => 'required|integer'
        ]);

        $currentLocale = \App::getLocale();
        $question->correctAnswers = $request->correctAnswers;
        $question->translateOrNew($currentLocale)->text = $request->text;
        $question->save();

        //Loop through all changed response options
        if($request->response_option_text) {
        foreach($request->response_option_text as $response_option_id => $response_option_text) {
            $response_option = ResponseOption::find($response_option_id);
            $response_option->text = $response_option_text;
            $response_option->isCorrectAnswer = in_array($response_option_id, $request->response_option_correct);
            $response_option->save();
        }

        //Loop through all deleted response options
        if($request->remove_response_option_text) {
                foreach($request->remove_response_option_text as $response_option_id => $response_option_text) {
                    ResponseOption::destroy($response_option_id);
                }
            }
        }

        //Loop through all added response options
        if($request->new_response_option_text) {
            foreach($request->new_response_option_text as $response_option_id => $response_option_text) {
                $response_option = new ResponseOption;
                $response_option->text = $response_option_text;
                if($request->new_response_option_correct) {
                    $response_option->isCorrectAnswer = in_array($response_option_id, $request->new_response_option_correct);
                } else {
                    $response_option->isCorrectAnswer = false;
                }
                $response_option->question_id = $question->id;
                $response_option->save();
            }
        }

        logger("QuestionController@update, redirecting to ".'/lessons/'.$question->lesson->id.'/edit');

        return redirect('/lessons/'.$question->lesson->id.'/edit')->with('success', 'Ändringar sparade');
    }
}
