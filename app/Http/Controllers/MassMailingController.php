<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Workplace;

class MassMailingController extends Controller
{
    public function store(Request $request) {
        usleep(50000);

        $this->validate($request, [
            'subject' => 'required',
            'body' => 'required',
            'workplaces' => 'required',
        ]);

        logger(Auth::user()->name." is doing a mass mailing to ".count($request->workplaces)." workplaces.");

        $amountsent = 0;
        $amountfailed = 0;

        foreach($request->workplaces as $workplace_id) {
            $workplace = Workplace::find($workplace_id);
            foreach($workplace->users->whereNotNull('email') as $user) {
                $to = [];
                $to[] = ['email' => $user->email, 'name' => $user->name];
                
                try {
                    \Mail::to($to)->send(new \App\Mail\MassMailing($request->subject, $request->body));
                    $amountsent++;
                } catch(\Swift_TransportException $e) {
                    logger("Couldn't send mail to ".$user->email);
                    $amountfailed++;
                }
            }
        }

        logger('Mass mailing finished. Mail sent successfully to '.$amountsent.' addresses and failed sending to '.$amountfailed.' addresses.');

        return redirect('/')->with('success', __('Meddelandet har skickats ut till :amountsent mottagare', ['amountsent' => $amountsent]));
    }

    public function create() {
        $data = [
            'workplaces' => Workplace::all(),
        ];

        return view('massmailing.create')->with($data);
    }

}
