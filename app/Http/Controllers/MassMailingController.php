<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Workplace;
use App\Poll;
use App\User;

class MassMailingController extends Controller
{
    public function store(Request $request) {
        usleep(50000);

        $this->validate($request, [
            'subject' => 'required',
            'body' => 'required',
            'users' => 'required',
        ],
        [
            'subject.required' => __('Du måste ange ett ett ärende!'),
            'body.required' => __('Du måste skriva någon text i mailet!'),
            'users.required' => __('Du måste välja minst en mottagare!'),
        ]);

        logger(Auth::user()->name." is doing a mass mailing to ".count($request->users)." users.");

        $amountsent = 0;
        $amountfailed = 0;

        $poll = null;
        if($request->poll) {
            $poll = Poll::find($request->poll);
        }

        foreach($request->users as $user_id) {
            $user = User::find($user_id);
            $to = [];
            $to[] = ['email' => $user->email, 'name' => $user->name];
            
            try {
                //logger("Sending mail to ".$user->email);
                \Mail::to($to)->send(new \App\Mail\MassMailing($request->subject, $request->body));
                $amountsent++;
            } catch(\Swift_TransportException $e) {
                logger("Couldn't send mail to ".$user->email);
                $amountfailed++;
            }
        }

        logger('Mass mailing finished. Mail sent successfully to '.$amountsent.' addresses and failed sending to '.$amountfailed.' addresses.');

        return redirect('/')->with('success', __('Meddelandet har skickats ut till :amountsent mottagare', ['amountsent' => $amountsent]));
    }

    public function create(Request $request) {

        $data = [
            'users' => User::whereNotNull('email')->whereNotNull('workplace_id')->orderBy('name')->get(),
            'connectedPoll' => Poll::find($request->poll),
        ];

        return view('massmailing.create')->with($data);
    }

}
