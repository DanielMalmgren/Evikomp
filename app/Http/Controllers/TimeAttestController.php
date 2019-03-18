<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\ActiveTime;
use App\ProjectTimeType;
use App\TimeAttest;
use App\Workplace;

class TimeAttestController extends Controller
{
    public function create(Request $request) {
        $project_time_types = ProjectTimeType::all();
        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all();
        } else {
            $workplaces = Auth::user()->admin_workplaces;
        }

        foreach($workplaces as $wp) {
            logger($wp->name.': '.$wp->pivot->attestlevel);
        }

        $data = array(
            'workplaces' => $workplaces
        );
        return view('timeattest.create')->with($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'month' => 'required',
            'workplace' => 'required'
        ]);

        $year = date('Y', strtotime($request->month." month"));
        $month = date('n', strtotime($request->month." month"));
        $user = Auth::user();

        logger(print_r($request->level2attest, true));

        if(isset($request->level2attest)) {
            foreach($request->level2attest as $user_id) {
                $time_attest = new TimeAttest;
                $time_attest->year = $year;
                $time_attest->month = $month;
                $time_attest->user_id = $user_id;
                $time_attest->attestant_id = $user->id;
                $time_attest->attestlevel = 2;
                $time_attest->hours = User::find($user_id)->time_attests->where('month', $month)->where('year', $year)->first()->hours;
                $time_attest->clientip = $request->ip();
                $time_attest->authnissuer = session('authnissuer');
                $time_attest->save();
            }
        }

        if(isset($request->level3attest)) {
            foreach($request->level3attest as $user_id) {
                $time_attest = new TimeAttest;
                $time_attest->year = $year;
                $time_attest->month = $month;
                $time_attest->user_id = $user_id;
                $time_attest->attestant_id = $user->id;
                $time_attest->attestlevel = 3;
                $time_attest->hours = User::find($user_id)->time_attests->where('month', $month)->where('year', $year)->first()->hours;
                $time_attest->clientip = $request->ip();
                $time_attest->authnissuer = session('authnissuer');
                $time_attest->save();
            }
        }

        return redirect('/')->with('success', 'Attesteringen har sparats');
    }

    //Month is relative to current month, so 0 is this month and -1 is last month
    public function ajaxuserlist(Workplace $workplace, $month, Request $request) {
        $year = date('Y', strtotime($month." month"));
        $month = date('n', strtotime($month." month"));

        //TODO: Det här är inget snyggt, måste komma på hur man gör det på ett bättre sätt!
        foreach(Auth::user()->admin_workplaces as $admin_workplace) {
            if($admin_workplace->id == $workplace->id) {
                $attestlevel = $admin_workplace->pivot->attestlevel;
            }
        }

        $data = array(
            'workplace' => $workplace,
            'attestlevel' => $attestlevel,
            'year' => $year,
            'month' => $month
        );

        return view('timeattest.ajaxuserlist')->with($data);
    }

    public function ajaxuserdetails(User $user, $year, $month) {

        $time_rows = $user->time_rows($year, $month);

        $data = array(
            'time_rows' => $time_rows,
            'year' => $year,
            'month' => $month
        );

        return view('timeattest.ajaxuserdetails')->with($data);
    }

}
