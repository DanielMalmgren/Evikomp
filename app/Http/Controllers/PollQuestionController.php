<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\PollQuestion;

class PollQuestionController extends Controller
{
    public function show(PollQuestion $question): View {

        $data = [
            'question' => $question,
        ];
        return view('pollquestions.show')->with($data);
    }
}
