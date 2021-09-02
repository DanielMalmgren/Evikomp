<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ActiveTime;
use App\ProjectTime;
use App\TimeAttest;
use App\User;
use App\Workplace;
use App\Municipality;
use App\Lesson;

class CreateStatisticsXls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:createstatisticsxls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Excel file with global statistics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./public/xls-template/Statistik.xlsx');

        $atusers = null;

        foreach(Municipality::filter()->orderBy('name')->get() as $municipality) {
            $this->info($municipality->name);
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

        $this->info("Global total");
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

        $writer->save("storage/app/public/Statistik Evikomp.xlsx");
    }
}
