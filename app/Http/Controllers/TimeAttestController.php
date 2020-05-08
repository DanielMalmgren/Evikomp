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

        $user = Auth::user();

        if(isset($request->level2attest)) {
            foreach($request->level2attest as $user_id) {
                TimeAttest::updateOrCreate([
                    'year' => $request->year,
                    'month' => $request->month,
                    'user_id' => $user_id,
                    'attestlevel' => 2,
                ],
                [
                    'attestant_id' => $user->id,
                    'authnissuer' => session('authnissuer'),
                    'hours' => User::find($user_id)->time_attests->where('month', $request->month)->where('year', $request->year)->first()->hours,
                    'clientip' => $request->ip(),
                ]);
            }
        }

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
                    'hours' => User::find($user_id)->time_attests->where('month', $request->month)->where('year', $request->year)->first()->hours,
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

        if(ClosedMonth::where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->exists()) {
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
