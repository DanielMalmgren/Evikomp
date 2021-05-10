<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\TimeAttest;
use App\Workplace;
use App\ClosedMonth;
use App\ProjectTime;

class TimeAttestController extends Controller
{
    public function create() {
        $user = Auth::user();
        setlocale(LC_TIME, $user->locale_id);

        if ($user->hasRole('Admin')) {
            $workplaces = Workplace::filter()->get()->sortBy('name');
        } else {
            $workplaces = Auth::user()->admin_workplaces->filter()->sortBy('name');
        }

        $year = date('Y', strtotime("first day of previous month"));
        $month = date('n', strtotime("first day of previous month"));

        $data = [
            'year' => $year,
            'month' => $month,
            'workplaces' => $workplaces,
        ];
        return view('timeattest.create')->with($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'month' => 'required',
            'year' => 'required',
            'workplace' => 'required',
        ]);

        $manager = app('impersonate');
        if($manager->isImpersonating()) {
            return redirect('/')->with('error', 'Du kan inte attestera som någon annan!');
        }

        $user = Auth::user();

        if(isset($request->level3attest)) {
            foreach($request->level3attest as $user_id) {
                TimeAttest::updateOrCreate([
                    'year' => $request->year,
                    'month' => $request->month,
                    'user_id' => $user_id,
                    'attestlevel' => 3,
                ],
                [
                    'attestant_id' => $user->id,
                    'authnissuer' => session('authnissuer'),
                    'hours' => User::find($user_id)->time_attests->where('attestlevel', 1)->where('month', $request->month)->where('year', $request->year)->sum('hours'),
                    'clientip' => $request->ip(),
                ]);
            }
        }

        return redirect('/timeattest/create')->with('success', 'Attesteringen har sparats');
    }

    public function ajaxuserlist(Workplace $workplace, $year, $month) {
        $attestlevel = 0;
        if (Auth::user()->hasRole('Admin')) {
            $attestlevel = 100;
        } else {
            //TODO: Det här är inget snyggt, måste komma på hur man gör det på ett bättre sätt!
            foreach(Auth::user()->admin_workplaces as $admin_workplace) {
                if($admin_workplace->id === $workplace->id) {
                    $attestlevel = $admin_workplace->pivot->attestlevel;
                }
            }
        }

        $data = [
            'workplace' => $workplace,
            'attestlevel' => $attestlevel,
            'year' => $year,
            'month' => $month,
        ];

        return view('timeattest.ajaxuserlist')->with($data);
    }

    public function ajaxuserdetails(User $user, $year, $month) {

        $time_rows = $user->time_rows($year, $month);

        $data = [
            'time_rows' => $time_rows,
            'year' => $year,
            'month' => $month,
        ];

        return view('timeattest.ajaxuserdetails')->with($data);
    }

    //Create time attests from signatures on attendee list
    public function from_list(Request $request, ProjectTime $project_time) {
        $user = Auth::user();
        if(!$user->hasRole('Admin') && ! $project_time->workplace->workplace_admins->contains('id', $user->id)) {
            abort(403);
        }

        usleep(50000);
        $request->validate([
            'users' => 'required',
            'signing_boss' => 'required',
        ],
        [
            'users.required' => __('Du måste ange minst en användare som har skrivit under närvaron!'),
            'signing_boss.required' => __('Du måste ange namnet på den chef som skrivit under närvaron!'),
        ]);

        $project_time->users()->sync($request->users);

        $year = $project_time->year;
        $month = $project_time->month;
        $hours = $project_time->minutes/60;

        foreach($project_time->users as $user) {
            logger("Creating attest for ".$user->name);

            if(!$user->month_is_fully_attested($year, $month, $hours, 1)) {
                $attest = new TimeAttest();
                $attest->year = $year;
                $attest->month = $month;
                $attest->user_id = $user->id;
                $attest->attestant_id = $user->id;
                $attest->attestlevel = 1;
                $attest->authnissuer = session('authnissuer');
                $attest->hours = $hours;
                $attest->clientip = $request->ip();
                $attest->from_list_by = Auth::user()->id;
                $attest->project_time_id = $project_time->id;
                $attest->save();
            }

            if(!$user->month_is_fully_attested($year, $month, $hours, 3)) {
                $attest = new TimeAttest();
                $attest->year = $year;
                $attest->month = $month;
                $attest->user_id = $user->id;
                $attest->attestant_id = $request->signing_boss;
                $attest->attestlevel = 3;
                $attest->authnissuer = session('authnissuer');
                $attest->hours = $hours;
                $attest->clientip = $request->ip();
                $attest->from_list_by = Auth::user()->id;
                $attest->project_time_id = $project_time->id;
                $attest->save();
            }
        }

        return redirect('/projecttime/'.$project_time->workplace_id)->with('success', __('Lärtillfället har nu markerats som attesterat'));
    }
}
