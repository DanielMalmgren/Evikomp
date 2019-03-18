<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\TimeAttest;

class TimeAttestLevel1Controller extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'month' => 'required',
            'attest' => 'required'
        ]);

        $year = date('Y', strtotime($request->month." month"));
        $month = date('n', strtotime($request->month." month"));
        $user = Auth::user();

        $time_attest = new TimeAttest;
        $time_attest->year = $year;
        $time_attest->month = $month;
        $time_attest->user_id = $user->id;
        $time_attest->attestant_id = $user->id;
        $time_attest->attestlevel = 1;
        $time_attest->hours = $request->hours;
        $time_attest->clientip = $request->ip();
        $time_attest->authnissuer = session('authnissuer');
        $time_attest->save();

        return redirect('/')->with('success', 'Attesteringen har sparats');
    }

    public function create() {
        setlocale(LC_TIME, \Auth::user()->locale_id);
        return view('timeattestlevel1.create');
    }

    //Month is relative to current month, so 0 is this month and -1 is last month
    public function ajax($month, User $user = null, Request $request) {
        if(!$user) {
            $user = Auth::user();
        }

        $year = date('Y', strtotime($month." month"));
        $month = date('n', strtotime($month." month"));

        $time_rows = $user->time_rows($year, $month);

        $data = array(
            'time_rows' => $time_rows,
            'year' => $year,
            'month' => $month
        );
        return view('timeattestlevel1.ajax')->with($data);
    }
}
