<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ActiveTime;
use App\User;
use DateInterval;
use App\Exports\ActiveTimeExport;
use Maatwebsite\Excel\Facades\Excel;

class ActiveTimeController extends Controller
{
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

    private static $timecells = array(
        1 => 'E13',
        2 => 'F13',
        3 => 'G13',
        4 => 'H13',
        5 => 'I13',
        6 => 'J13',
        7 => 'K13',
        8 => 'L13',
        9 => 'M13',
        10 => 'N13',
        11 => 'O13',
        12 => 'P13',
        13 => 'Q13',
        14 => 'R13',
        15 => 'S13',
        16 => 'T13',
        17 => 'U13',
        18 => 'V13',
        19 => 'W13',
        20 => 'X13',
        21 => 'Y13',
        22 => 'Z13',
        23 => 'AA13',
        24 => 'AB13',
        25 => 'AC13',
        26 => 'AD13',
        27 => 'AE13',
        28 => 'AF13',
        29 => 'AG13',
        30 => 'AH13',
        31 => 'AI13'
    );

    public function export(User $user = null, Request $request) {
        if(!$user) {
            $user = Auth::user();
        }

        if($request->year) {
            $year = $request->year;
        } else {
            $year = date('Y');
        }

        if($request->month) {
            $month = $request->month;
            $monthstr = strftime('%B', strtotime('2000-'.$request->month.'-15'));
        } else {
            $month = date('n');
            $monthstr = strftime('%B');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./xls-template/Närvarorapport_deltagare.xlsx');

        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getCell('C6')->setValue($user->name); //Namn på deltagare
        $worksheet->getCell('C7')->setValue(substr_replace($user->personid, '-', 8, 0)); //Personnummer
        $worksheet->getCell('C8')->setValue($user->workplace->municipality->name); //Namn på deltagarens arbetsgivare
        $worksheet->getCell('C9')->setValue($user->workplace->municipality->orgnummer); //Organisationsnummer

        $worksheet->getCell('W3')->setValue('2018/00079'); //Diarienummer
        $worksheet->getCell('W4')->setValue('Evikomp'); //Projektnamn

        setlocale(LC_TIME, 'sv_SE');
        $worksheet->getCell('W6')->setValue($monthstr); //Redovisningsmånad
        $worksheet->getCell('W7')->setValue($year); //År

        $worksheet->getCell('A13')->setValue('Tid i Evikomps webapp');

        for($i = 1; $i <= 31; $i++) {
            $this_time = $active_times_db = ActiveTime::where('user_id', $user->id)->whereMonth('date', $month)->whereYear('date', $year)->whereDay('date', $i)->first();
            if($this_time) {
                $worksheet->getCell(self::$timecells[$i])->setValue(round($this_time->seconds/3600, 1));
            }
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = "Närvaro Evikomp ".$monthstr." ".$year." ".$user->name.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $writer->save("php://output");
    }
}
