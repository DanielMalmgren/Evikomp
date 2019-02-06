<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ActiveTime;
use App\User;
use DateInterval;
use App\Exports\ActiveTimeExport;
use Maatwebsite\Excel\Facades\Excel;

class ActiveTimeController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'time' => 'required'
        ]);

        $activetime = ActiveTime::firstOrNew(
            ['user_id' => Auth::user()->id,
            'date' => date('Y-m-d')]
        );

        $activetime->seconds += $request->time;
        $activetime->save();
    }

    public function export(User $user = null) {
        if(!$user) {
            $user = Auth::user();
        }

        return Excel::download(new ActiveTimeExport($user), 'Aktiv_tid_Evikomp.xlsx');
    }
}
