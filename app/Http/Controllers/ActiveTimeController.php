<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ActiveTime;
use App\User;
use DateInterval;
use App\ProjectTime;
use App\ProjectTimeType;
use App\Exports\ActiveTimeExport;
use Maatwebsite\Excel\Facades\Excel;

class ActiveTimeController extends Controller
{
    public function show() {
        setlocale(LC_TIME, \Auth::user()->locale_id);
        return view('activetime.show');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'time' => 'required'
        ]);

        $activetime = ActiveTime::firstOrNew(
            ['user_id' => Auth::user()->id,
            'date' => date('Y-m-d')]
        );

        $activetime->seconds += $request->time;
        $activetime->save();
    }

    public function export(User $user = null, Request $request) {
        if(!$user) {
            $user = Auth::user();
        }

        if($request->year) {
            $year = $request->year;
        } else {
            $year = date('Y');
        }

        setlocale(LC_TIME, 'sv_SE');
        if($request->month) {
            $month = $request->month;
            $monthstr = strftime('%B', strtotime('2000-'.$request->month.'-15'));
            //logger("Månad enligt strftime med locale ".\App::getLocale(LC_TIME).": ".$monthstr);
            //$monthstr = \Carbon\Carbon::parse('2000-'.$request->month.'-15')->formatLocalized('%B');
            //logger("Månad enligt formatLocalized med locale ".\Carbon\Carbon::getLocale().": ".$monthstr);
        } else {
            $month = date('n');
            $monthstr = strftime('%B');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./xls-template/Närvarorapport_deltagare.xlsx');

        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->setCellValue('C6', $user->name); //Namn på deltagare
        $worksheet->setCellValue('C7', substr_replace($user->personid, '-', 8, 0)); //Personnummer
        $worksheet->setCellValue('C8', $user->workplace->municipality->name); //Namn på deltagarens arbetsgivare
        $worksheet->setCellValue('C9', $user->workplace->municipality->orgnummer); //Organisationsnummer

        $worksheet->setCellValue('W3', '2018/00079'); //Diarienummer
        $worksheet->setCellValue('W4', 'Evikomp'); //Projektnamn

        $worksheet->setCellValue('W6', ucfirst($monthstr)); //Redovisningsmånad
        $worksheet->setCellValue('W7', $year); //År

        $worksheet->setCellValue('A13', 'Tid i Evikomps webapp');

        for($i = 1; $i <= 31; $i++) {
            $this_time = $active_times_db = ActiveTime::where('user_id', $user->id)->whereMonth('date', $month)->whereYear('date', $year)->whereDay('date', $i)->first();
            if($this_time) {
                $worksheet->setCellValueByColumnAndRow($i+4,13,round($this_time->seconds/3600, 1));
            }
        }

        $types = $user->project_times()->groupBy('project_time_type_id')->pluck('project_time_type_id');

        //logger("Typer: ".print_r($types, true));
        //TODO: Ta bort lite loggning. Kolla också om det inte finns något smidigare sätt att göra databasfrågorna...

        $row = 14;
        foreach($types as $type) {
            $typename = ProjectTimeType::find($type)->name;
            $worksheet->setCellValueByColumnAndRow(1,$row,$typename);
            logger("Typ: ".print_r($type, true));
            $dates = $user->project_times()->where('project_time_type_id', $type)->groupBy('date')->pluck('date');
            //logger("Datumn: "$date));
            foreach($dates as $date) {
                logger("Datum: ".$date);
                $occasions = $user->project_times()->where('project_time_type_id', $type)->where('date', $date)->get();
                $minutes = 0;
                foreach($occasions as $occasion) {
                    logger("Tillfälle: ".$occasion->minutes());
                     $minutes += $occasion->minutes();
                }
                $day = date('j', strtotime($date));
                logger("Dag: ".$day.", minuter: ".$minutes);
                $worksheet->setCellValueByColumnAndRow($day+4,$row,round($minutes/60, 1));
            }
            $row++;
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = "Närvaro Evikomp ".$monthstr." ".$year." ".$user->name.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . addslashes(utf8_decode($filename)) . '";filename*=utf-8\'\'' . rawurlencode($filename));
        $writer->save("php://output");
    }
}
