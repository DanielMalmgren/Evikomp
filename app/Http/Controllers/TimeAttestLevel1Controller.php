<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\TimeAttest;
use App\ClosedMonth;

class TimeAttestLevel1Controller extends Controller
{
    public function store(Request $request) {
        usleep(50000);

        $this->validate($request, [
            'month' => 'required',
            'year' => 'required',
            'attest' => 'required',
        ]);

        $user = Auth::user();

        TimeAttest::updateOrCreate([
            'year' => $request->year,
            'month' => $request->month,
            'user_id' => $user->id,
            'attestant_id' => $user->id,
            'attestlevel' => 1,
        ],
        [
            'authnissuer' => session('authnissuer'),
            'hours' => $request->hours,
            'clientip' => $request->ip(),
        ]);

        return redirect('/')->with('success', 'Attesteringen har sparats');
    }

    public function create() {
        $user = Auth::user();
        setlocale(LC_TIME, $user->locale_id);

        if(ClosedMonth::where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->exists()) {
            $month_is_closed = true;
        } else {
            $month_is_closed = false;
        }

        $year = date('Y', strtotime("first day of previous month"));
        $month = date('n', strtotime("first day of previous month"));
        $monthstr = strftime('%B', strtotime("first day of previous month"));

        $time_rows = $user->time_rows($year, $month);

        $data = [
            'time_rows' => $time_rows,
            'year' => $year,
            'month' => $month,
            'monthstr' => $monthstr,
            'days_in_month' => cal_days_in_month(CAL_GREGORIAN, $month, $year),
            'already_attested' => $user->time_attests->where('attestlevel', 1)->where('month', $month)->where('year', $year)->isNotEmpty(),
            'month_is_closed' => $month_is_closed,
        ];

        return view('timeattestlevel1.create')->with($data);
    }

    public function manualattestxls(Request $request) {
        $user = Auth::user();
        setlocale(LC_TIME, 'sv_SE');

        $year = date('Y', strtotime("first day of previous month"));
        $month = date('n', strtotime("first day of previous month"));
        $monthstr = strftime('%B', strtotime("first day of previous month"));
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./xls-template/Närvarorapport_deltagare.xlsx');

        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->setCellValue('C6', $user->name); //Namn på deltagare
        $worksheet->setCellValue('C7', substr_replace($user->personid, '-', 8, 0)); //Personnummer
        $worksheet->setCellValue('C8', $user->workplace->municipality->name); //Namn på deltagarens arbetsgivare
        $worksheet->setCellValue('C9', $user->workplace->municipality->orgnummer); //Organisationsnummer

        $worksheet->setCellValue('W3', '2020/00088'); //Diarienummer
        $worksheet->setCellValue('W4', 'Evikomp 2.0'); //Projektnamn

        $worksheet->setCellValue('W6', ucfirst($monthstr)); //Redovisningsmånad
        $worksheet->setCellValue('W7', $year); //År

        $time_rows = $user->time_rows($year, $month);

        $excelrow = 13;
        $hours = 0;

        foreach($time_rows as $title => $time_row) {
            if($time_row != end($time_rows)) { //Skip the last row which contains sum, not needed in Excel
                $worksheet->setCellValueByColumnAndRow(1,$excelrow,$title);
                for($day = 1; $day <= $days_in_month; $day++) {
                    if(isset($time_row[$day])) {
                        $worksheet->setCellValueByColumnAndRow($day+4,$excelrow,$time_row[$day]);
                        $hours += $time_row[$day];
                    }
                }
                $excelrow++;
            }
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = "Närvaro Evikomp ".$monthstr." ".$year." ".$user->name.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . addslashes(utf8_decode($filename)) . '";filename*=utf-8\'\'' . rawurlencode($filename));
        $writer->save("php://output");

        TimeAttest::updateOrCreate([
            'year' => $year,
            'month' => $month,
            'user_id' => $user->id,
            'attestant_id' => $user->id,
            'attestlevel' => 0,
            'authnissuer' => 'Manuell attestering',
        ],
        [
            'hours' => $hours,
            'clientip' => $request->ip(),
        ]);
    }
}
