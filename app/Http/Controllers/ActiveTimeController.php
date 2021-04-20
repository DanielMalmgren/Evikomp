<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ActiveTime;

class ActiveTimeController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'time' => 'required|integer',
        ]);

        $activetime = ActiveTime::firstOrNew([
            'user_id' => Auth::user()->id,
            'date' => date('Y-m-d'),
        ]);

        $activetime->seconds += $request->time;
        $activetime->save();

        if (!\App::environment('prod')) {
            logger('Storing '.$request->time.' seconds for '.Auth::user()->name.' for a total of '.$activetime->seconds.' ('.round($activetime->seconds/60).' minutes) ('.substr($request->header('User-Agent'), 0, 25).'...)');
        }
    }
}
