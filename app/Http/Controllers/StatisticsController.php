<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActiveTime;
use App\User;
use App\Workplace;
use App\Lesson;
use App\Charts\SessionChart;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index() {

        $chart = new SessionChart();
        //$chartdata = ActiveTime::whereDate('date', '>',Carbon::now()->subDays(3))->groupBy('date')->get();

        $data = collect([]);
        $labels = collect([]);

        for ($days_backwards = 14; $days_backwards >= 0; $days_backwards--) {
            // Could also be an array_push if using an array rather than a collection.
            $data->push(ActiveTime::whereDate('date', today()->subDays($days_backwards))->count());
            $labels->push(today()->subDays($days_backwards)->toDateString());
        }
        //logger(print_r($data, true));
        $chart->labels($labels);
        $chart->dataset('Inloggningar per dag', 'line', $data);
        $chart->height(100);

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
