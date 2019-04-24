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

        if(!isset($request->anonymous)) {
            $name = Auth::user()->name;
            $email = Auth::user()->email;
        } else {
            $name = __('Anonym användare');
            $email = env('MAIL_FROM_ADDRESS');
        }

        $to[] = array('email' => env('FEEDBACK_RECIPIENT_ADDRESS'), 'name' => env('FEEDBACK_RECIPIENT_NAME'));
        $from[] = array('email' => $email, 'name' => $name);

        \Mail::to($to)->from($from)->send(new \App\Mail\Feedback($request->content, $name));

        return redirect('/')->with('success', 'Din feedback har skickats!');
    }
}
