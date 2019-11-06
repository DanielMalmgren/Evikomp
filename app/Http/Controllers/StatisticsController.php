<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActiveTime;
use App\ProjectTime;
use App\TimeAttest;
use App\User;
use App\Workplace;
use App\Lesson;
use App\Charts\SessionChart;

class StatisticsController extends Controller
{
    public function index() {

        $chart = new SessionChart();
        //$chartdata = ActiveTime::whereDate('date', '>',Carbon::now()->subDays(3))->groupBy('date')->get();

        $logins = collect([]);
        $time = collect([]);
        $labels = collect([]);

        for ($days_backwards = 14; $days_backwards >= 0; $days_backwards--) {
            $logins->push(ActiveTime::whereDate('date', today()->subDays($days_backwards))->count());
            $time->push(ActiveTime::whereDate('date', today()->subDays($days_backwards))->sum('seconds')/60);
            $labels->push(today()->subDays($days_backwards)->toDateString());
        }

        $chart->options([
            'maintainAspectRatio' => false,
            'scales'              => [
                'xAxes' => [],
                'yAxes' => [ [
                            'type' => 'linear',
                            'display' => true,
                            'position' => 'left',
                            'id' => 'y-logins'],
                             [
                            'type' => 'linear',
                            'display' => true,
                            'position' => 'right',
                             'id' => 'y-time'],
                            [
                            'ticks' => [
                                'beginAtZero' => true,
                            ],
                        'position' => 'right'],
                ],
            ],
        ]);

        //logger(print_r($data, true));
        $chart->labels($labels);
        $chart->dataset('Antal inloggade personer per dag', 'line', $logins)->options([
            'borderColor' => 'rgba(255, 0, 0, 0.3)',
            'backgroundColor' => 'rgba(255, 0, 0, 0.1)',
            'borderWidth' => '3',
            'yAxisID' => 'y-logins'
        ]);
        $chart->dataset('Totalt inloggade minuter per dag', 'line', $time)->options([
            'borderColor' => 'rgba(0, 255, 0, 0.3)',
            'backgroundColor' => 'rgba(0, 255, 0, 0.1)',
            'borderWidth' => '3',
            'yAxisID' => 'y-time'
        ]);
        $chart->height(100);

        $projectTime = ProjectTime::all();

        //$attestedLevel1 = TimeAttest::where('level', 1)->sum('hours');
        //$attestedLevel2 = TimeAttest::where('level', 2)->sum('hours');
        //$attestedLevel3 = TimeAttest::where('level', 3)->sum('hours');

        $data = [
            'sessions' => ActiveTime::whereDate('date', '=', date('Y-m-d'))->count(),
            'users' => User::count(),
            'workplaces' => Workplace::count(),
            'lessons' => Lesson::count(),
            'totalactivehours' => round(ActiveTime::sum('seconds')/3600, 1),
            'totalprojecthours' => round($projectTime->sum('minutes')/60, 1),
            'chart' => $chart,
            'attestedhourslevel1' => TimeAttest::where('attestlevel', 1)->sum('hours'),
            'attestedhourslevel2' => TimeAttest::where('attestlevel', 2)->sum('hours'),
            'attestedhourslevel3' => TimeAttest::where('attestlevel', 3)->sum('hours'),
        ];

        return view('statistics.index')->with($data);
    }
}
