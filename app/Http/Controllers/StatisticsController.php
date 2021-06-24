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
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index() {
        setlocale(LC_NUMERIC, \Auth::user()->locale_id);

        //$allusers = User::gdpraccepted()->count();
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
            'users' => $filteredusers,
            'maleusers' => $maleusers,
            'femaleusers' => $femaleusers,
            'workplaces' => Workplace::filter()->count(),
            'lessons' => Lesson::where('active', true)->count(),
            'totalactivehours' => $totalactivehours,
            'averageactivehours' => $averageactivehours,
            'totalprojecthours' => round(ProjectTime::all()->sum('minutes_total')/60),
            'attestedhourslevel1' => $attestedhourslevel1,
            'attestedhourslevel1peruser' => round($attestedhourslevel1/$filteredusers, 2),
            'attestedhourslevel1permale' => round($maleattestedhourslevel1/$maleusers, 2),
            'attestedhourslevel1perfemale' => round($femaleattestedhourslevel1/$femaleusers, 2),
            'attestedhourslevel3' => $attestedhourslevel3,
            'attestedhourslevel3peruser' => round($attestedhourslevel3/$filteredusers, 2),
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

    public function export() {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./xls-template/Statistik.xlsx');

        foreach(Municipality::filter()->orderBy('name')->get() as $municipality) {
            $worksheet = clone $spreadsheet->getSheetByName('Evikomp totalt');
            $worksheet->setTitle($municipality->name);
            $spreadsheet->addSheet($worksheet);

            $row = 3;
            $celldate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            while($celldate != null && $row < 1000) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($celldate);
                $year = $date->format('Y');
                $month = $date->format('n');
                $lastdayinmonth = $date->format("Y-m-t");
                if($date>new \DateTime()) {
                    $future = true;
                } else {
                    $future = false;
                }
    
                if(!$future) {
                    //New workplaces
                    $value = Workplace::where('municipality_id', $municipality->id)->whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
                    $worksheet->setCellValueByColumnAndRow(2, $row, $value);
    
                    //Total workplaces
                    $value = Workplace::where('municipality_id', $municipality->id)->whereDate('created_at', '<=', $lastdayinmonth)->count();
                    $worksheet->setCellValueByColumnAndRow(3, $row, $value);

                    //New users
                    $value = User::whereIn('workplace_id', $municipality->workplaces->filter()->pluck('id'))->whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
                    $worksheet->setCellValueByColumnAndRow(4, $row, $value);
    
                    //Total users
                    $value = User::whereIn('workplace_id', $municipality->workplaces->filter()->pluck('id'))->whereDate('created_at', '<=', $lastdayinmonth)->count();
                    $worksheet->setCellValueByColumnAndRow(5, $row, $value);
    
                    //Users active in platform
                    $active_times = ActiveTime::whereYear('date', $year)->whereMonth('date', $month)->groupBy('user_id');
                    $atusers = User::whereIn('workplace_id', $municipality->workplaces->filter()->pluck('id'))->whereIn('id', $active_times->pluck('user_id'))->get();
                    $value = $atusers->count();
                    $worksheet->setCellValueByColumnAndRow(6, $row, $value);
                }
    
                //Users active in project time
                $ptusers = collect();
                $project_times = ProjectTime::whereYear('date', $year)->whereMonth('date', $month)->get();
               foreach($project_times as $project_time) {
                    $ptusers = $ptusers->whereIn('workplace_id', $municipality->workplaces->filter()->pluck('id'))->merge($project_time->users)->unique('id');
                }
                $value = $ptusers->count();
                $worksheet->setCellValueByColumnAndRow(7, $row, $value);
    
                //Users active in either platform or project time
                if($future) {
                    $users = $ptusers;
                } else {
                    $users = $atusers->merge($ptusers)->unique('id');
                }
                $value = $users->count();
                $worksheet->setCellValueByColumnAndRow(8, $row, $value);
    
                if(!$future) {
                    //Active time in platform
                    $attime = round(ActiveTime::filter()->whereIn('user_id', $municipality->users->pluck('id'))->whereYear('date', $year)->whereMonth('date', $month)->sum('seconds')/3600);
                    $worksheet->setCellValueByColumnAndRow(9, $row, $attime);
                }
    
                //Project time
                $pttime = round($project_times->whereIn('workplace_id', $municipality->workplaces->pluck('id'))->sum('minutes_total')/60);
                $worksheet->setCellValueByColumnAndRow(10, $row, $pttime);
    
                if(!$future) {
                    //Attested time by users
                    $value = round(TimeAttest::whereIn('user_id', $municipality->users->pluck('id'))->where('year', $year)->where('month', $month)->where('attestlevel', 1)->sum('hours'));
                    $worksheet->setCellValueByColumnAndRow(13, $row, $value);
    
                    //Attested time by managers
                    $value = round(TimeAttest::whereIn('user_id', $municipality->users->pluck('id'))->where('year', $year)->where('month', $month)->where('attestlevel', 3)->sum('hours'));
                    $worksheet->setCellValueByColumnAndRow(14, $row, $value);
                }
    
                $row++;
                $celldate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            }
    
        }

        $worksheet = $spreadsheet->getSheetByName('Evikomp totalt');

        $row = 3;
        $celldate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
        while($celldate != null && $row < 1000) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($celldate);
            $year = $date->format('Y');
            $month = $date->format('n');
            $lastdayinmonth = $date->format("Y-m-t");
            if($date>new \DateTime()) {
                $future = true;
            } else {
                $future = false;
            }

            if(!$future) {
                //New workplaces
                $value = Workplace::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
                $worksheet->setCellValueByColumnAndRow(2, $row, $value);

                //Total workplaces
                $value = Workplace::whereDate('created_at', '<=', $lastdayinmonth)->count();
                $worksheet->setCellValueByColumnAndRow(3, $row, $value);

                //New users
                $value = User::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
                $worksheet->setCellValueByColumnAndRow(4, $row, $value);

                //Total users
                $value = User::whereDate('created_at', '<=', $lastdayinmonth)->count();
                $worksheet->setCellValueByColumnAndRow(5, $row, $value);

                //Users active in platform
                $active_times = ActiveTime::whereYear('date', $year)->whereMonth('date', $month)->groupBy('user_id');
                $atusers = User::whereIn('id', $active_times->pluck('user_id'))->get();
                $value = $atusers->count();
                $worksheet->setCellValueByColumnAndRow(6, $row, $value);
            }

            //Users active in project time
            $ptusers = collect();
            $project_times = ProjectTime::whereYear('date', $year)->whereMonth('date', $month)->get();
            foreach($project_times as $project_time) {
                $ptusers = $ptusers->merge($project_time->users)->unique('id');
            }
            $value = $ptusers->count();
            $worksheet->setCellValueByColumnAndRow(7, $row, $value);

            //Users active in either platform or project time
            if($future) {
                $users = $ptusers;
            } else {
                $users = $atusers->merge($ptusers)->unique('id');
            }
            $value = $users->count();
            $worksheet->setCellValueByColumnAndRow(8, $row, $value);

            if(!$future) {
                //Active time in platform
                $attime = round(ActiveTime::filter()->whereYear('date', $year)->whereMonth('date', $month)->sum('seconds')/3600);
                $worksheet->setCellValueByColumnAndRow(9, $row, $attime);
            }

            //Project time
            $pttime = round($project_times->sum('minutes_total')/60);
            $worksheet->setCellValueByColumnAndRow(10, $row, $pttime);

            if(!$future) {
                //Attested time by users
                $value = round(TimeAttest::where('year', $year)->where('month', $month)->where('attestlevel', 1)->sum('hours'));
                $worksheet->setCellValueByColumnAndRow(13, $row, $value);

                //Attested time by managers
                $value = round(TimeAttest::where('year', $year)->where('month', $month)->where('attestlevel', 3)->sum('hours'));
                $worksheet->setCellValueByColumnAndRow(14, $row, $value);
            }

            $row++;
            $celldate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
        }

        $value = "Denna fil genererades ".date("Y-m-d H:i:s");
        $worksheet->setCellValueByColumnAndRow(1, $row+1, $value);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = "Statistik Evikomp.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $writer->save("php://output");
    }
}
