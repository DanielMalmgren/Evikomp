<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\TimeAttest;
use App\Workplace;
use App\ClosedMonth;

class TimeAttestController extends Controller
{
    public function create() {
        $user = Auth::user();
        setlocale(LC_TIME, $user->locale_id);

        if ($user->hasRole('Admin')) {
            $workplaces = Workplace::all()->sortBy('name');
        } else {
            $workplaces = Auth::user()->admin_workplaces->sortBy('name');
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

        $user = Auth::user();

        if(isset($request->level2attest)) {
            foreach($request->level2attest as $user_id) {
                $time_attest = new TimeAttest();
                $time_attest->year = $request->year;
                $time_attest->month = $request->month;
                $time_attest->user_id = $user_id;
                $time_attest->attestant_id = $user->id;
                $time_attest->attestlevel = 2;
                $time_attest->hours = User::find($user_id)->time_attests->where('month', $request->month)->where('year', $request->year)->first()->hours;
                $time_attest->clientip = $request->ip();
                $time_attest->authnissuer = session('authnissuer');
                $time_attest->save();
            }
        }

        if(isset($request->level3attest)) {
            foreach($request->level3attest as $user_id) {
                $time_attest = new TimeAttest();
                $time_attest->year = $request->year;
                $time_attest->month = $request->month;
                $time_attest->user_id = $user_id;
                $time_attest->attestant_id = $user->id;
                $time_attest->attestlevel = 3;
                $time_attest->hours = User::find($user_id)->time_attests->where('month', $request->month)->where('year', $request->year)->first()->hours;
                $time_attest->clientip = $request->ip();
                $time_attest->authnissuer = session('authnissuer');
                $time_attest->save();
            }
        }

        return redirect('/')->with('success', 'Attesteringen har sparats');
    }

    public function ajaxuserlist(Workplace $workplace, $year, $month) {
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

        if(ClosedMonth::all()->where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->isNotEmpty()) {
            $month_is_closed = true;
        } else {
            $month_is_closed = false;
        }

        $data = [
            'workplace' => $workplace,
            'attestlevel' => $attestlevel,
            'year' => $year,
            'month' => $month,
            'month_is_closed' => $month_is_closed,
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

}
