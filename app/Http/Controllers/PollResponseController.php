<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Poll;
use App\PollResponse;
use App\PollQuestion;
use App\PollSession;

class PollResponseController extends Controller
{
    public function store(Request $request) {
        /*usleep(50000);
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

        return $this->update($request, $question);*/
        logger("Poll session ID:");
        logger(session("poll_session_id"));

        if(isset($request->response)) {
            logger("Response:");
            logger(print_r($request->response, true));
            foreach($request->response as $id => $response) {
                $poll_response = new PollResponse();
                $poll_response->poll_question_id = $id;
                $poll_response->poll_session_id = session("poll_session_id");
                if(is_array($response)) {
                    $poll_response->response = implode(", ", $response);
                } elseif(is_null($response)) {
                    $poll_response->response = "";
                } else {
                    $poll_response->response = $response;
                }
                $poll_response->save();
            }
        }
        /*if(isset($request->multiresponse)) {
            logger("Multiesponse:");
            logger(print_r($request->multiresponse, true));
        }*/

        $question = PollQuestion::find($request->page_break_id);
        //$poll = $question->poll;
        if(isset($question)) {
            $nextquestion = $question->next_question();
        }

        if(isset($nextquestion)) {
            return redirect('/pollquestion/'.$nextquestion->id);
        } else {
            $poll_session = PollSession::find(session("poll_session_id"));
            $poll_session->finished = true;
            $poll_session->save();
            return view('polls.feedback');
        }
    }
}
