<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\PollQuestion;

class PollQuestionController extends Controller
{
    public function show(PollQuestion $question): View {

        $previous = $question->first_on_previous_page();
        if(isset($previous)) {
            $previous_id = $previous->id;
        } else {
            $previous_id = null;
        }

        $data = [
            'question' => $question,
            'previous_id' => $previous_id,
        ];

        return view('pollquestions.show')->with($data);
    }
}
