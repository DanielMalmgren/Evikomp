<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\ResponseOption;
use App\TestSession;
use App\TestResponse;
use App\Lesson;

class QuestionController extends Controller
{
    public function show(Request $request, Question $question) {
        $test_session = TestSession::find($request->query('testsession_id'));

        if(!isset($test_session)) {
            logger("No test session found for session id ".$request->query('testsession_id'));
            logger("Current URL: ".url()->full());
            logger("Previous URL: ".url()->previous());
        }

        $test_response = TestResponse::firstOrCreate(
            [
                'test_session_id' => $test_session->id,
                'question_id' => $question->id,
            ]
        );

        $request->session()->put('test_response_id', $test_response->id);

        $data = [
            'question' => $question,
            'test_session' => $test_session,
            'test_response' => $test_response,
        ];
        return view('questions.show')->with($data);
    }

    public function create(Request $request) {
        $lesson_id = $request->input('lesson_id');
        $data = [
            'lesson_id' => $lesson_id,
        ];
        return view('questions.create')->with($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'text' => 'required',
            'correctAnswers' => 'required|integer',
            'lesson_id' => 'required',
        ]);

        $lesson = Lesson::find($request->lesson_id);

        $question = new Question();
        $question->lesson_id = $lesson->id;
        $question->order = $lesson->questions->max('order')+1;
        $question->save();

        return $this->update($request, $question);
    }

    public function destroy(Question $question) {
        logger('Destroying question '.$question->id);
        $question->delete();

        $following_questions = Question::where('order', '>', $question->order)->where('lesson_id', $question->lesson_id)->get();
        foreach($following_questions as $following_question) {
            $following_question->order--;
            $following_question->save();
        }
    }

    public function edit(Question $question) {
        $data = [
            'question' => $question,
        ];
        return view('questions.edit')->with($data);
    }

    public function reorder(Request $request) {
        parse_str($request->data, $data);
        $ids = $data['id'];

        foreach($ids as $order => $id){
            $question = Question::findOrFail($id);
            $question->order = $order+1;
            $question->save();
        }
    }

    public function update(Request $request, Question $question) {
        $this->validate($request, [
            'text' => 'required',
            'correctAnswers' => 'required|integer',
            'new_response_option_text.*' => 'string',
            'response_option_text.*' => 'string',
        ],
        [
            'text.required' => __('Du glömde skriva i själva frågan!'),
            'new_response_option_text.*.string' => __('Du kan inte ange tomma svarsalternativ!'),
            'response_option_text.*.string' => __('Du kan inte ange tomma svarsalternativ!'),
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
                $response_option->isCorrectAnswer = isset($request->response_option_correct) && in_array($response_option_id, $request->response_option_correct);
                $response_option->save();
            }
        }

        //Loop through all deleted response options
        if($request->remove_response_option_text) {
            foreach($request->remove_response_option_text as $response_option_id => $response_option_text) {
                ResponseOption::destroy($response_option_id);
            }
        }

        //Loop through all added response options
        if($request->new_response_option_text) {
            foreach($request->new_response_option_text as $response_option_id => $response_option_text) {
                $response_option = new ResponseOption();
                $response_option->text = $response_option_text;
                if($request->new_response_option_correct) {
                    $response_option->isCorrectAnswer = isset($request->new_response_option_correct) && in_array($response_option_id, $request->new_response_option_correct);
                } else {
                    $response_option->isCorrectAnswer = false;
                }
                $response_option->question_id = $question->id;
                $response_option->save();
            }
        }

        return redirect('/lessons/'.$question->lesson->id.'/editquestions')->with('success', 'Ändringar sparade');
    }
}
