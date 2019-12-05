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

        $data = [
            'sessions' => ActiveTime::filter()->whereDate('date', '=', date('Y-m-d'))->count(),
            'users' => User::gdpraccepted()->count(),
            'workplaces' => Workplace::filter()->count(),
            'lessons' => Lesson::count(),
            'totalactivehours' => round(ActiveTime::filter()->sum('seconds')/3600),
            'totalprojecthours' => round(ProjectTime::all()->sum('minutes_total')/60),
            'attestedhourslevel1' => round(TimeAttest::where('attestlevel', 1)->sum('hours')),
            'attestedhourslevel3' => round(TimeAttest::where('attestlevel', 3)->sum('hours')),
        ];

        return view('statistics.index')->with($data);
    }

    public function ajaxchart($chartid) {
        setlocale(LC_NUMERIC, \Auth::user()->locale_id);

        $chart = new Chart();
        $labels = collect([]);

        switch ($chartid) {
            case 1: //Logins history chart
                $heading = _('Aktivitet senaste två veckorna');
                $logins = collect([]);
                $time = collect([]);

                for ($days_backwards = 14; $days_backwards >= 0; $days_backwards--) {
                    $logins->push(ActiveTime::filter()->whereDate('date', today()->subDays($days_backwards))->count());
                    $time->push(round(ActiveTime::filter()->whereDate('date', today()->subDays($days_backwards))->sum('seconds')/3600), 1);
                    $labels->push(today()->subDays($days_backwards)->toDateString());
                }

                $chart->dataset(_('Antal inloggade personer per dag'), 'line', $logins)->options([
                    'borderColor' => 'rgba(255, 0, 0, 0.3)',
                    'backgroundColor' => 'rgba(255, 0, 0, 0.1)',
                    'borderWidth' => '3',
                ]);
                $chart->dataset(_('Totalt inloggade timmar per dag'), 'line', $time)->options([
                    'borderColor' => 'rgba(0, 255, 0, 0.3)',
                    'backgroundColor' => 'rgba(0, 255, 0, 0.1)',
                    'borderWidth' => '3',
                ]);
                $chart->height(350);

                break;
            case 2: //Time per workplace chart
                $heading = _('Totalt slutattesterad tid per arbetsplats');
                $timeperwp = collect([]);

                foreach(Workplace::filter()->get() as $workplace) {
                    $timeperwp->push(round($workplace->total_attested_time(3)));
                    $labels->push($workplace->name);
                }
                $chart->dataset(_('Tid per arbetsplats'), 'pie', $timeperwp);
                $chart->displayLegend(false);
                $chart->displayAxes(false);
                break;
            case 3: //Total attested time chart
                $heading = _('Totalt slutattesterad tid (ackumulerat)');
                $attestedtime = collect([]);

                $firstattesteddate = TimeAttest::whereNotNull('created_at')->where('attestlevel', 3)->oldest()->first()->created_at;
                $period = new \Carbon\CarbonPeriod($firstattesteddate, \Carbon\Carbon::tomorrow());
                $currentlyattested = TimeAttest::where('attestlevel', 3)->sum('hours');
                foreach ($period as $date) {
                    $attestedtodate = round(TimeAttest::where('created_at', '<=', $date)->where('attestlevel', 3)->sum('hours'));
                    if($attestedtodate > $currentlyattested*0.05) {
                        $attestedtime->push($attestedtodate);
                        $labels->push($date->format('Y-m-d'));
                    }
                }
                $chart->dataset(_('Attesterad tid'), 'line', $attestedtime);
                $chart->displayLegend(false);
                break;
            default:
                logger("Unknown chart id!");
        }

        $chart->labels($labels);

        $data = [
            'heading' => $heading,
            'chart' => $chart,
        ];

        return view('statistics.ajaxchart')->with($data);
    }
}
