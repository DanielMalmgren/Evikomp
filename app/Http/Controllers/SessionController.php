<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActiveTime;

class SessionController extends Controller
{
    public function index() {

        $data = [
            'sessions' => ActiveTime::whereDate('date', '=', date('Y-m-d'))->orderBy('updated_at')->get(),
        ];

        return view('sessions.index')->with($data);
    }
}
