<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Municipality;
use App\User;
use App\ActiveTime;

class TimeSummaryController extends Controller
{
    public function show() {
        setlocale(LC_TIME, \Auth::user()->locale_id);
        return view('timesummary.show');
    }

    public function export(Request $request) {
        if($request->year) {
            $year = $request->year;
        } else {
            $year = date('Y');
        }

        setlocale(LC_TIME, 'sv_SE');
        if($request->month) {
            $month = $request->month;
            $monthstr = strftime('%B', strtotime('2000-'.$request->month.'-15'));
        } else {
            $month = date('n');
            $monthstr = strftime('%B');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./xls-template/Sammanställning_deltagare.xlsx');

        $worksheet = $spreadsheet->getSheetByName('Intyg för närvarotid');
        $worksheet->setCellValue('B8', '2018/00079'); //Diarienummer
        $worksheet->setCellValue('B9', 'Evikomp'); //Projektnamn
        $worksheet->setCellValue('D8', ucfirst($monthstr)); //Redovisningsmånad
        $worksheet->setCellValue('D9', $year); //År

        $worksheet = $spreadsheet->getSheetByName('Register_deltag_organisationer');
        $row = 6;
        foreach(Municipality::all()->sortBy('name') as $municipality) {
            $worksheet->setCellValueByColumnAndRow(1,$row,$municipality->name);
            $worksheet->setCellValueByColumnAndRow(2,$row,$municipality->orgnummer);
            $worksheet->setCellValueByColumnAndRow(3,$row,'Kommun');
            $row++;
        }

        $worksheet = $spreadsheet->getSheetByName('Deltagarförteckning');
        $worksheet->setCellValue('B3', '2018/00079'); //Diarienummer
        $worksheet->setCellValue('B4', 'Evikomp'); //Projektnamn
        $worksheet->setCellValue('H3', ucfirst($monthstr)); //Redovisningsmånad
        $worksheet->setCellValue('H4', $year); //År
        $row = 9;
        foreach(User::all()->sortBy('name') as $user) {
            $projecttime = 0;
            foreach($user->project_times()->get() as $pt) {
                //logger("Minuter: ".$pt->minutes());
                $projecttime += $pt->minutes();
            }

            $webtime = 0;
            foreach(ActiveTime::where('user_id', $user->id)->whereMonth('date', $month)->whereYear('date', $year)->get() as $daytime) {
                $webtime += $daytime->seconds;
            }

            $totaltime = round($projecttime/60 + $webtime/3600, 1);

            if($totaltime > 0) {
                $worksheet->setCellValueByColumnAndRow(1,$row,$user->name);
                $worksheet->setCellValueByColumnAndRow(2,$row,substr_replace($user->personid, '-', 8, 0));
                $worksheet->setCellValueByColumnAndRow(6,$row,$user->workplace->municipality->name);
                $worksheet->setCellValueByColumnAndRow(7,$row,$user->workplace->municipality->orgnummer);
                $worksheet->setCellValueByColumnAndRow(8,$row,$totaltime);
                $worksheet->setCellValueByColumnAndRow(14,$row,substr($user->created_at, 0, 10));
                $row++;
            }
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = "Sammanställning Evikomp ".$monthstr." ".$year.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $writer->save("php://output");
    }
}
