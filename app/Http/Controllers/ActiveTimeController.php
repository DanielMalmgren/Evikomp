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

        if (!\App::environment('prod')) {
            logger('Storing '.$request->time.' seconds for '.Auth::user()->name.' ('.substr($request->header('User-Agent'), 0, 40).'...)');
        }

        $activetime->seconds += $request->time;
        $activetime->save();
    }
}
