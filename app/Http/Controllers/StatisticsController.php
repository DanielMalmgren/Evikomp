<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActiveTime;
use App\User;
use App\Workplace;
use App\Lesson;

class StatisticsController extends Controller
{
    public function index() {

        $data = [
            'sessions' => ActiveTime::whereDate('date', '=', date('Y-m-d'))->count(),
            'users' => User::count(),
            'workplaces' => Workplace::count(),
            'lessons' => Lesson::count(),
        ];

        return view('statistics.index')->with($data);
    }
}
