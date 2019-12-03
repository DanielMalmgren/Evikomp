<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActiveTime;
use App\ProjectTime;
use App\TimeAttest;
use App\User;
use App\Workplace;
use App\Lesson;
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

        $timeperworkplacechart = new Chart();
        $timeperwp = collect([]);
        $labels = collect([]);

        foreach(Workplace::filter()->get() as $workplace) {
            $timeperwp->push(round($workplace->total_attested_time(3)));
            $labels->push($workplace->name);
        }
        $timeperworkplacechart->labels($labels);
        $timeperworkplacechart->dataset('Tid per arbetsplats', 'pie', $timeperwp);
        $timeperworkplacechart->displayLegend(false);
        $timeperworkplacechart->displayAxes(false);

        $attestedtimechart = new Chart();
        $attestedtime = collect([]);
        $labels = collect([]);

        $firstattesteddate = TimeAttest::whereNotNull('created_at')->where('attestlevel', 3)->oldest()->first()->created_at;
        $period = new \Carbon\CarbonPeriod($firstattesteddate, today());
        foreach ($period as $date) {
            $attestedtime->push(TimeAttest::where('created_at', '<=', $date)->where('attestlevel', 3)->sum('hours'));
            $labels->push($date->format('Y-m-d'));
        }
        $attestedtimechart->labels($labels);
        $attestedtimechart->dataset('Attesterad tid', 'line', $attestedtime);
        $attestedtimechart->displayLegend(false);

        $data = [
            'sessions' => ActiveTime::filter()->whereDate('date', '=', date('Y-m-d'))->count(),
            'users' => User::gdpraccepted()->count(),
            'workplaces' => Workplace::filter()->count(),
            'lessons' => Lesson::count(),
            'totalactivehours' => round(ActiveTime::filter()->sum('seconds')/3600),
            'totalprojecthours' => round(ProjectTime::all()->sum('minutes_total')/60),
            'attestedhourslevel1' => round(TimeAttest::where('attestlevel', 1)->sum('hours')),
            'attestedhourslevel3' => round(TimeAttest::where('attestlevel', 3)->sum('hours')),
            'loginshistorychart' => $loginshistorychart,
            'timeperworkplacechart' => $timeperworkplacechart,
            'attestedtimechart' => $attestedtimechart,
        ];

        return view('statistics.index')->with($data);
    }
}
