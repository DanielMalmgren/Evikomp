<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActiveTime;
use App\ProjectTime;
use App\TimeAttest;
use App\User;
use App\Workplace;
use App\Municipality;
use App\Lesson;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class StatisticsController extends Controller
{
    public function index() {
        setlocale(LC_NUMERIC, \Auth::user()->locale_id);

        $allusers = User::gdpraccepted()->count();
        $filteredusers = User::filter()->count();
        $maleusers = User::gender('M')->filter()->count();
        $femaleusers = User::gender('F')->filter()->count();

        $totalactivehours = round(ActiveTime::filter()->sum('seconds')/3600);
        $averageactivehours = round($totalactivehours/$filteredusers, 2);

        $attestedhourslevel1 = round(TimeAttest::where('attestlevel', 1)->sum('hours'));
        $maleattestedhourslevel1 = round(TimeAttest::gender('M')->where('attestlevel', 1)->sum('hours'));
        $femaleattestedhourslevel1 = round(TimeAttest::gender('F')->where('attestlevel', 1)->sum('hours'));
        $attestedhourslevel3 = round(TimeAttest::where('attestlevel', 3)->sum('hours'));
        $maleattestedhourslevel3 = round(TimeAttest::gender('M')->where('attestlevel', 3)->sum('hours'));
        $femaleattestedhourslevel3 = round(TimeAttest::gender('F')->where('attestlevel', 3)->sum('hours'));

        $data = [
            'sessions' => ActiveTime::filter()->whereDate('date', '=', date('Y-m-d'))->count(),
            'users' => $allusers,
            'maleusers' => $maleusers,
            'femaleusers' => $femaleusers,
            'workplaces' => Workplace::filter()->count(),
            'lessons' => Lesson::where('active', true)->count(),
            'totalactivehours' => $totalactivehours,
            'averageactivehours' => $averageactivehours,
            'totalprojecthours' => round(ProjectTime::all()->sum('minutes_total')/60),
            'attestedhourslevel1' => $attestedhourslevel1,
            'attestedhourslevel1peruser' => round($attestedhourslevel1/$allusers, 2),
            'attestedhourslevel1permale' => round($maleattestedhourslevel1/$maleusers, 2),
            'attestedhourslevel1perfemale' => round($femaleattestedhourslevel1/$femaleusers, 2),
            'attestedhourslevel3' => $attestedhourslevel3,
            'attestedhourslevel3peruser' => round($attestedhourslevel3/$allusers, 2),
            'attestedhourslevel3permale' => round($maleattestedhourslevel3/$maleusers, 2),
            'attestedhourslevel3perfemale' => round($femaleattestedhourslevel3/$femaleusers, 2),
        ];

        return view('statistics.index')->with($data);
    }

    public function ajaxchart($chartid) {
        setlocale(LC_NUMERIC, \Auth::user()->locale_id);

        $chart = new Chart();
        $labels = collect([]);
        $heading = "";

        switch ($chartid) {
            case 1: //Logins history chart
                $heading = _('Aktivitet senaste två veckorna');
                $logins = collect([]);
                $time = collect([]);

                for ($days_backwards = 14; $days_backwards >= 0; $days_backwards--) {
                    $logins->push(ActiveTime::filter()->whereDate('date', today()->subDays($days_backwards))->count());
                    $time->push(round(ActiveTime::filter()->whereDate('date', today()->subDays($days_backwards))->sum('seconds')/3600, 1));
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
                    $timeperwp->push(round($workplace->time_attests()->where('attestlevel', 3)->sum('hours')));
                    $labels->push($workplace->name);
                }
                $chart->dataset(_('Tid per arbetsplats'), 'pie', $timeperwp)->options([
                    'backgroundColor' => $this->generateColors($timeperwp),
                ]);
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
            case 4: //Time per municipality chart
                $heading = _('Totalt slutattesterad tid per kommun');
                $time = collect([]);

                foreach(Municipality::all() as $municipality) {
                    $time->push(round($municipality->time_attests()->where('attestlevel', 3)->sum('hours')));
                    $labels->push($municipality->name);
                }
                $chart->dataset(_('Tid per kommun'), 'pie', $time)->options([
                    'backgroundColor' => $this->generateColors($time),
                ]);
                $chart->displayLegend(false);
                $chart->displayAxes(false);
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

    private function generateColors($collection): array
    {
        $colors = [];
        for ($i = 0; $i < $collection->count(); $i++) {
            $colors[] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }
        return $colors;
    }
}
