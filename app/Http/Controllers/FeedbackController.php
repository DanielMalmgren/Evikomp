<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lesson;
use App\TestSession;

class FeedbackController extends Controller
{
    public function create() {

        $lessons = Lesson::orderBy('track_id')->orderBy('order')->where('active', true)->get();

        if(strpos(url()->previous(), '/lessons/')) {
            $activelesson = substr(url()->previous(), strrpos(url()->previous(), '/')+1);
        } elseif(strpos(url()->previous(), '/result/')) {
            $testsession = TestSession::find(substr(url()->previous(), strrpos(url()->previous(), '/')+1));
            $activelesson = $testsession->lesson_id;
        } else {
            $activelesson = null;
        }

        $data = [
            'lessons' => $lessons,
            'activelesson' => $activelesson,
        ];

        return view('feedback.create')->with($data);
    }

    public function post(Request $request) {
        $this->validate($request, [
            'content' => 'required',
        ]);

        if(!isset($request->anonymous)) {
            $name = Auth::user()->name;
            $email = Auth::user()->email;
            $mobile = Auth::user()->mobile;
            $workplace = Auth::user()->workplace->name;
        } else {
            $name = __('Anonym användare');
            $email = env('MAIL_FROM_ADDRESS');
            $mobile = '';
            $workplace = '';
        }

        $to = [];
        $to[] = ['email' => env('FEEDBACK_RECIPIENT_ADDRESS'), 'name' => env('FEEDBACK_RECIPIENT_NAME')];

        \Mail::to($to)->send(new \App\Mail\Feedback($request->content, $request->lesson, $name, $email, $mobile, $workplace, $request->contacted));

        return redirect('/')->with('success', 'Din feedback har skickats!');
    }
}
