<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function create(Request $request) {
        return view('feedback.create');
    }

    public function post(Request $request) {
        $this->validate($request, [
            'content' => 'required'
        ]);

        $to[] = array('email' => 'daniel.malmgren@itsam.se', 'name' => 'Daniel Malmgren');

        $body = $request->content;
        if(!isset($request->anonymous)) {
            $body .= "\r\n\r\n".Auth::user()->name;
        }
        \Mail::to($to)->send(new \App\Mail\Feedback($body));

        return redirect('/')->with('success', 'Din feedback har skickats!');
    }
}
