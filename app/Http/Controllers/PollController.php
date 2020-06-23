<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Poll;
use App\PollSession;

class PollController extends Controller
{
    public function show(Poll $poll): View {
        $poll_session = new PollSession();
        $poll_session->poll_id = $poll->id;
        $poll_session->user_id = Auth::user()->id;
        $poll_session->save();

        $first_question_id = $poll->first_question()->id;

        $data = [
            'poll' => $poll,
            'poll_session' => $poll_session,
            'first_question_id' => $first_question_id,
        ];
        return view('polls.show')->with($data);
    }
}
