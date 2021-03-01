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
            'prev_month' => 'required',
            'prev_month_year' => 'required',
            'prev_month_hours' => 'required',
            'this_month' => 'required',
            'this_month_year' => 'required',
            'this_month_hours' => 'required',
            'attest' => 'required',
        ]);

        $user = Auth::user();

        $attest = new TimeAttest();
        $attest->year = $request->prev_month_year;
        $attest->month = $request->prev_month;
        $attest->user_id = $user->id;
        $attest->attestant_id = $user->id;
        $attest->attestlevel = 1;
        $attest->authnissuer = session('authnissuer');
        $attest->hours = $request->prev_month_hours;
        $attest->clientip = $request->ip();
        $attest->save();

        $attest = new TimeAttest();
        $attest->year = $request->this_month_year;
        $attest->month = $request->this_month;
        $attest->user_id = $user->id;
        $attest->attestant_id = $user->id;
        $attest->attestlevel = 1;
        $attest->authnissuer = session('authnissuer');
        $attest->hours = $request->this_month_hours;
        $attest->clientip = $request->ip();
        $attest->save();

        /*TimeAttest::updateOrCreate([
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
        ]);*/

        return redirect('/')->with('success', 'Attesteringen har sparats');
    }

    public function create() {
        $user = Auth::user();
        setlocale(LC_TIME, $user->locale_id);

        /*if(ClosedMonth::where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->exists()) {
            $month_is_closed = true;
        } else {
            $month_is_closed = false;
        }*/

        $prev_month_year = date('Y', strtotime("first day of previous month"));
        $prev_month = date('n', strtotime("first day of previous month"));
        $prev_month_str = strftime('%B', strtotime("first day of previous month"));
        $prev_month_time_rows = $user->time_rows($prev_month_year, $prev_month);
        $days_in_prev_month = cal_days_in_month(CAL_GREGORIAN, $prev_month, $prev_month_year);
        $attested_prev_month = $user->attested_time_month($prev_month, $prev_month_year, 1);

        logger("Attested prev month: ".$attested_prev_month);

        $this_month_year = date('Y');
        $this_month = date('n');
        $this_month_str = strftime('%B');
        $this_month_time_rows = $user->time_rows($this_month_year, $this_month);
        $days_in_this_month = cal_days_in_month(CAL_GREGORIAN, $this_month, $this_month_year);
        $attested_this_month = $user->attested_time_month($this_month, $this_month_year, 1);

        $already_fully_attested = $user->month_is_fully_attested($prev_month_year, $prev_month) 
                               && $user->month_is_fully_attested($this_month_year, $this_month);

        $data = [
            'prev_month_year' => $prev_month_year,
            'prev_month' => $prev_month,
            'prev_month_str' => $prev_month_str,
            'prev_month_time_rows' => $prev_month_time_rows,
            'days_in_prev_month' => $days_in_prev_month,
            'attested_prev_month' => $attested_prev_month,
            'this_month_year' => $this_month_year,
            'this_month' => $this_month,
            'this_month_str' => $this_month_str,
            'this_month_time_rows' => $this_month_time_rows,
            'days_in_this_month' => $days_in_this_month,
            'attested_this_month' => $attested_this_month,
            'already_fully_attested' => $already_fully_attested,
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
