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
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class StatisticsController extends Controller
{
    public function index() {
        setlocale(LC_NUMERIC, \Auth::user()->locale_id);

        $loginshistorychart = new Chart();
        $logins = collect([]);
        $time = collect([]);
        $labels = collect([]);

        for ($days_backwards = 14; $days_backwards >= 0; $days_backwards--) {
            $logins->push(ActiveTime::filter()->whereDate('date', today()->subDays($days_backwards))->count());
            $time->push(round(ActiveTime::filter()->whereDate('date', today()->subDays($days_backwards))->sum('seconds')/3600), 1);
            $labels->push(today()->subDays($days_backwards)->toDateString());
        }

        $loginshistorychart->labels($labels);
        $loginshistorychart->dataset('Antal inloggade personer per dag', 'line', $logins)->options([
            'borderColor' => 'rgba(255, 0, 0, 0.3)',
            'backgroundColor' => 'rgba(255, 0, 0, 0.1)',
            'borderWidth' => '3',
        ]);
        $loginshistorychart->dataset('Totalt inloggade timmar per dag', 'line', $time)->options([
            'borderColor' => 'rgba(0, 255, 0, 0.3)',
            'backgroundColor' => 'rgba(0, 255, 0, 0.1)',
            'borderWidth' => '3',
        ]);
        $loginshistorychart->height(350);

        $projectTime = ProjectTime::all();

        $timeperworkplacechart = new Chart();
        $time = collect([]);
        $labels = collect([]);

        foreach(Workplace::filter()->get() as $workplace) {
            $time->push(round($workplace->total_attested_time(3), 1));
            $labels->push($workplace->name);
        }
        $timeperworkplacechart->labels($labels);
        $timeperworkplacechart->dataset('Tid per arbetsplats', 'pie', $time);
        $timeperworkplacechart->options([
            'legend' => ['display' => false]
        ]);

        $data = [
            'sessions' => ActiveTime::filter()->whereDate('date', '=', date('Y-m-d'))->count(),
            'users' => User::gdpraccepted()->count(),
            'workplaces' => Workplace::filter()->count(),
            'lessons' => Lesson::count(),
            'totalactivehours' => round(ActiveTime::filter()->sum('seconds')/3600, 1),
            'totalprojecthours' => round($projectTime->sum('minutes_total')/60, 1),
            'attestedhourslevel1' => TimeAttest::where('attestlevel', 1)->sum('hours'),
            'attestedhourslevel2' => TimeAttest::where('attestlevel', 2)->sum('hours'),
            'attestedhourslevel3' => TimeAttest::where('attestlevel', 3)->sum('hours'),
            'loginshistorychart' => $loginshistorychart,
            'timeperworkplacechart' => $timeperworkplacechart,
        ];

        return view('statistics.index')->with($data);
    }
}
