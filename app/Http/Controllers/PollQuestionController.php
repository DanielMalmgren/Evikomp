<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\PollQuestion;
use App\PollSession;

class PollQuestionController extends Controller
{
    public function show(PollQuestion $question): View {

        $previous = $question->first_on_previous_page();
        if(isset($previous)) {
            $previous_id = $previous->id;
        } else {
            $previous_id = null;
        }

        $poll_session = PollSession::find(session("poll_session_id"));

        $data = [
            'question' => $question,
            'previous_id' => $previous_id,
            'previous_responses' => $poll_session->poll_responses,
        ];

        return view('pollquestions.show')->with($data);
    }
}
