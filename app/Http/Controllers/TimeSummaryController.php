<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\ClosedMonth;
use App\Workplace;
use App\ActiveTime;
use App\ProjectTime;
use App\TimeAttest;

class TimeSummaryController extends Controller
{
    public function show() {
        setlocale(LC_TIME, \Auth::user()->locale_id);
        return view('timesummary.show');
    }

    public function ajax($rel_month) {
        setlocale(LC_TIME, \Auth::user()->locale_id);
        $time = incrementDate($rel_month);
        $year = date('Y', $time);
        $month = date('n', $time);

        $month_closed = ClosedMonth::where('month', $month)->where('year', $year)->exists();

        $projecthours = round(ActiveTime::filter()->whereYear('date', $year)->whereMonth('date', $month)->sum('seconds')/3600 +
                            ProjectTime::whereYear('date', $year)->whereMonth('date', $month)->get()->sum('minutes_total')/60, 1);

        $attestedhourslevel1 = round(TimeAttest::where('attestlevel', 1)->where('year', $year)->where('month', $month)->sum('hours'), 1);
        $attestedhourslevel2 = round(TimeAttest::where('attestlevel', 2)->where('year', $year)->where('month', $month)->sum('hours'), 1);
        $attestedhourslevel3 = round(TimeAttest::where('attestlevel', 3)->where('year', $year)->where('month', $month)->sum('hours'), 1);

        $data = [
            'year' => $year,
            'month' => $month,
            'monthstr' => strftime('%B', $time),
            'month_closed' => $month_closed,
            'workplaces' => Workplace::filter()->get(),
            'projecthours' => $projecthours,
            'attestedhourslevel1' => $attestedhourslevel1,
            'attestedhourslevel2' => $attestedhourslevel2,
            'attestedhourslevel3' => $attestedhourslevel3,
        ];

        return view('timesummary.ajax')->with($data);
    }

    public function wpdetails(Workplace $workplace, $year, $month) {
        $users = $workplace->users->merge($workplace->ptusers($month))->sortBy('name');

        $data = [
            'year' => $year,
            'month' => $month,
            'workplace' => $workplace,
            'users' => $users,
        ];

        return view('timesummary.wpdetails')->with($data);
    }

    private static function generateExcelRowForUser($worksheet, $row, $user, $hours, $colour=null) {
        $date_born=date_create(substr($user->personid, 0, 8));
        if($date_born) { //Because there are people with non date person id's
            $age = date_diff($date_born, date_create('now'))->y;
        } else {
            $age = 'okänd';
        }
        $gender = substr($user->personid, 10, 1)%2?"M":"K";
        if($user->project_times->isEmpty()) {
            $startdate = substr($user->created_at, 0, 10);
        } else {
            $startdate = min(substr($user->created_at, 0, 10), $user->project_times->sortBy('date')->first()->date);
        }

        $worksheet->setCellValueByColumnAndRow(1, $row, $user->name);                                 //Kolumn A, namn
        $worksheet->setCellValueByColumnAndRow(2, $row, substr_replace($user->personid, '-', 8, 0));  //Kolumn B, personnummer
        $worksheet->setCellValueByColumnAndRow(3, $row, $age);                                        //Kolumn C, ålder
        $worksheet->setCellValueByColumnAndRow(4, $row, $gender);                                     //Kolumn D, kön
        $worksheet->setCellValueByColumnAndRow(5, $row, $gender);                                     //Kolumn E, kön
        $worksheet->setCellValueByColumnAndRow(6, $row, $user->workplace->municipality->name);        //Kolumn F, organisationsnamn
        $worksheet->setCellValueByColumnAndRow(7, $row, $user->workplace->municipality->orgnummer);   //Kolumn G, organisationsnummer
        $worksheet->setCellValueByColumnAndRow(8, $row, $hours);                                      //Kolumn H, kompetensutvecklingstimmar
        $worksheet->setCellValueByColumnAndRow(12, $row, substr_replace($user->personid, '-', 8, 0)); //Kolumn L, personnummer
        $worksheet->setCellValueByColumnAndRow(13, $row, $hours);                                     //Kolumn H, antal timmar
        $worksheet->setCellValueByColumnAndRow(14, $row, $startdate);                                 //Kolumn N, Startdatum
        $worksheet->setCellValueByColumnAndRow(21, $row, $user->terms_of_employment);                 //Kolumn U, anställningsvillkor
        $worksheet->setCellValueByColumnAndRow(22, $row, $user->full_or_part_time);                   //Kolumn V, anställningens omfattning
        //$worksheet->setCellValueByColumnAndRow(23, $row, $user->email);                               //Kolumn W, e-postadress
        //$worksheet->setCellValueByColumnAndRow(24, $row, $user->mobile);                              //Kolumn X, mobilnummer

        if(isset($colour)) {
            $worksheet->getStyle('A'.$row.':H'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($colour);
        }
    }

    public function export(Request $request) {
        setlocale(LC_TIME, 'sv_SE');

        $this->validate($request, [
            'rel_month' => 'required',
        ]);

        $time = strtotime($request->rel_month." month");
        $year = date('Y', $time);
        $month = date('n', $time);
        $monthstr = strftime('%B', $time);

        if($request->close_month) {
            $closed_month = new ClosedMonth();
            $closed_month->user_id = Auth::user()->id;
            $closed_month->month = $month;
            $closed_month->year = $year;
            $closed_month->save();
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./xls-template/Sammanställning_deltagare.xlsx');

        $worksheet = $spreadsheet->getSheetByName('Deltagarförteckning');
        $worksheet->setCellValue('B3', '2020/00088'); //Diarienummer
        $worksheet->setCellValue('B4', 'Evikomp 2.0'); //Projektnamn
        $worksheet->setCellValue('H3', ucfirst($monthstr)); //Redovisningsmånad
        $worksheet->setCellValue('H4', $year); //År
        $row = 9;
        $municipalities = collect([]);
        $total_hours = 0;
        foreach(User::where('workplace_id', '!=', null)->orderBy('name')->get() as $user) {
            if($user->time_attests->where('attestlevel', 0)->where('month', $month)->where('year', $year)->count() > 0) {
                $totaltime = $user->time_attests->where('month', $month)->where('year', $year)->first()->hours;
                if($totaltime > 0) {
                    $total_hours += $totaltime;
                    if(!$municipalities->contains('id', $user->workplace->municipality->id)) {
                        $municipalities->push($user->workplace->municipality);
                    }

                    $this->generateExcelRowForUser($worksheet, $row, $user, $totaltime, 'ff0000');

                    $row++;
                }
            } elseif($user->time_attests->where('attestlevel', 3)->where('month', $month)->where('year', $year)->count() > 0) {
                $totaltime = $user->time_attests->where('month', $month)->where('year', $year)->first()->hours;
                if($totaltime > 0) {
                    $total_hours += $totaltime;
                    if(!$municipalities->contains('id', $user->workplace->municipality->id)) {
                        $municipalities->push($user->workplace->municipality);
                    }

                    $this->generateExcelRowForUser($worksheet, $row, $user, $totaltime);

                    $row++;
                }
            }
        }

        $worksheet = $spreadsheet->getSheetByName('Register_deltag_organisationer');
        $row = 6;
        foreach($municipalities->sortBy('name') as $municipality) {
            //logger("Timmar för ".$municipality->name.": ".$municipality->time);
            $worksheet->setCellValueByColumnAndRow(1, $row, $municipality->name);
            $worksheet->setCellValueByColumnAndRow(2, $row, $municipality->orgnummer);
            $worksheet->setCellValueByColumnAndRow(3, $row, 'Kommun');
            $row++;
        }

        $worksheet = $spreadsheet->getSheetByName('Intyg för närvarotid');
        $worksheet->setCellValue('B8', '2020/00088');       //Diarienummer
        $worksheet->setCellValue('B9', 'Evikomp 2.0');          //Projektnamn
        $worksheet->setCellValue('D8', ucfirst($monthstr)); //Redovisningsmånad
        $worksheet->setCellValue('D9', $year);              //År
        //$worksheet->setCellValue('C14', $total_hours);      //Totalt antal närvarotimmar under månaden
        $worksheet->setCellValue('C18', $municipalities->count()); //Totalt antal deltagande organisationer under månaden
        //$worksheet->setCellValue('C19', $total_hours);      //Totalt antal utbildningstimmar under månaden

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = "Sammanställning Evikomp ".$monthstr." ".$year.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $writer->save("php://output");
    }
}
