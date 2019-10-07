<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActiveTime;
use App\User;
use App\Workplace;
use App\Lesson;
use App\Charts\SessionChart;

class StatisticsController extends Controller
{
    public function index() {

        $chart = new SessionChart();
        $chartdata = ActiveTime::groupBy('date')->get();
        logger(print_r($chartdata, true));
        $chart->labels($chartdata->keys());
        $chart->dataset('Sessioner', 'line', $chartdata->values());

        $data = [
            'sessions' => ActiveTime::whereDate('date', '=', date('Y-m-d'))->count(),
            'users' => User::count(),
            'workplaces' => Workplace::count(),
            'lessons' => Lesson::count(),
            'chart' => $chart,
        ];

        return view('statistics.index')->with($data);
    }
}
