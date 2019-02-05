<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ActiveTime;
use DateInterval;

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
}
