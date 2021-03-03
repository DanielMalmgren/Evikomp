<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Workplace;
use App\ProjectTime;
use App\ProjectTimeType;
use App\ClosedMonth;
use PDF;

class ProjectTimeController extends Controller
{
    public function show($year, $month) {
        $user = Auth::user();
        setlocale(LC_TIME, $user->locale_id);

        $monthstr = strftime('%B', mktime(0, 0, 0, $month));
        if($month == 1) {
            $previous_month = 12;
            $previous_year = $year - 1;
            $next_month = 2;
            $next_year = $year;
        } elseif($month == 12) {
            $previous_month = 11;
            $previous_year = $year;
            $next_month = 1;
            $next_year = $year + 1;
        } else {
            $previous_month = $month - 1;
            $previous_year = $year;
            $next_month = $month + 1;
            $next_year = $year;
        }
        if($year == date('Y') && $month == date('n')) {
            $next_year = null;
            $next_month = null;
        }

        $time_rows = $user->time_rows($year, $month);

        $data = [
            'time_rows' => $time_rows,
            'year' => $year,
            'month' => $month,
            'monthstr' => $monthstr,
            'previous_month' => $previous_month,
            'previous_year' => $previous_year,
            'next_month' => $next_month,
            'next_year' => $next_year,
            'days_in_month' => cal_days_in_month(CAL_GREGORIAN, $month, $year),
        ];

        return view('projecttime.show')->with($data);
    }

    public function create() {
        $project_time_types = ProjectTimeType::all();
        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all()->sortBy('name');
        } else {
            $workplaces = Auth::user()->admin_workplaces->sortBy('name');
        }

        //If last month is closed, start the calendar picker on first day of this month
        //if(ClosedMonth::where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->exists()) {
        //    $mindate = date("Y-m")."-01";
        //} else {
            $mindate = date("Y-m", strtotime("first day of previous month"))."-01";
        //}

        $data = [
            'workplaces' => $workplaces,
            'project_time_types' => $project_time_types,
            'mindate' => $mindate,
        ];
        return view('projecttime.create')->with($data);
    }

    public function createsingleuser() {
        $project_time_types = ProjectTimeType::all();
        $user = Auth::user();

        //If last month is already attested, no further time may be registered on it, so start the calendar picker on first day of this month
        //The same goes if last month is closed by administrator
        //if($user->time_attests->where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->isNotEmpty() ||
        //   ClosedMonth::where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->exists()) {
        //    $mindate = date("Y-m")."-01";
        //} else {
            $mindate = date("Y-m", strtotime("first day of previous month"))."-01";
        //}

        $data = [
            'project_time_types' => $project_time_types,
            'user' => $user,
            'workplace' => $user->workplace,
            'mindate' => $mindate,
        ];
        return view('projecttime.createsingleuser')->with($data);
    }

    public function ajax(Workplace $workplace) {
        $project_time_types = ProjectTimeType::all();

        //If last month is closed, start the calendar picker on first day of this month
        //if(ClosedMonth::where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->exists()) {
        //    $mindate = date("Y-m")."-01";
        //} else {
            $mindate = date("Y-m", strtotime("first day of previous month"))."-01";
        //}

        $data = [
            'workplace' => $workplace,
            'project_time_types' => $project_time_types,
            'mindate' => $mindate,
        ];
        return view('projecttime.ajax')->with($data);
    }

    public function store(Request $request) {
        usleep(50000);
        $request->validate([
            'starttime' => 'required|date_format:H:i',
            'endtime' => 'required|date_format:H:i|after:starttime',
            'date' => 'required|date|before_or_equal:today|date_format:Y-m-d',
            'users' => 'required',
            'workplace_id' => 'required',
        ],
        [
            'starttime.required' => __('Du måste ange en starttid!'),
            'endtime.required' => __('Du måste ange en sluttid!'),
            'date.required' => __('Du måste ange ett datum!'),
            'date.date' => __('Datumet måste vara i formatet yyyy-mm-dd!'),
            'date.date_format' => __('Datumet måste vara i formatet yyyy-mm-dd!'),
            'date.before_or_equal' => __('Du kan inte registrera tid i framtiden!'),
            'starttime.date_format' => __('Tidpunkterna måste vara i formatet hh:mm!'),
            'endtime.date_format' => __('Tidpunkterna måste vara i formatet hh:mm!'),
            'users.required' => __('Du måste ange minst en användare som tid ska registreras på!'),
            'endtime.after' => __('Sluttiden får inte inträffa före starttiden!'),
        ]);

        $year = substr($request->date, 0, 4);
        $month = substr($request->date, 5, 2);

        //Loopa igenom alla de aktuella användarna
        //Ta fram deras tidsregistreringar för den aktuella dagen
        //För varje registrering, kolla så inte startdate eller enddate är mellan registrerigens start eller slut, kolla även tvärtemot (Så inte registreringen ligger inom vårt intervall)
        foreach($request->users as $user_id) {
            $user = User::find($user_id);

            //Checking for colliding attests
            /*if($user->time_attests->where('month', $month)->where('year', $year)->count() > 0) {
                //return back()->with('error', $user->name.' har redan attesterat denna månad!')->withInput();
                add_flash_message(
                    [
                        'message' => __(':name har redan attesterat denna månad!', ['name' => $user->name]),
                        'type' => 'danger',
                    ]
                );
            }*/

            //Checking for colliding registration
            $occasions = $user->project_times()->where('date', $request->date)->get();
            foreach($occasions as $occasion) {
                if(($request->starttime > $occasion->startstr() && $request->starttime < $occasion->endstr()) ||
                   ($request->endtime > $occasion->startstr() && $request->endtime < $occasion->endstr()) ||
                   ($occasion->starttime > $request->starttime && $occasion->startstr() < $request->endtime))  {
                    //return back()->with('error', $user->name.' har redan ett tillfälle inlagt mellan '.$occasion->startstr().' och '.$occasion->endstr().'!')->withInput();
                    add_flash_message([
                        'message' => __('Detta krockar med en registrering som :name har gjort mellan klockan :from och :to samma dag!', ['name' => $user->name, 'from' => $occasion->startstr(), 'to' =>$occasion->endstr()]),
                        'type' => 'danger',
                    ]);
                }
            }
        }

        if(! empty(Session('notification_collection'))) {
            return back()->withInput();
        }

        $workplace = Workplace::find($request->workplace_id);

        $project_time = new ProjectTime();
        $project_time->date = $request->date;
        $project_time->starttime = $request->starttime;
        $project_time->endtime = $request->endtime;
        $project_time->workplace_id = $workplace->id;
        $project_time->project_time_type_id = $request->type;
        $project_time->registered_by = Auth::user()->id;
        $project_time->save();
        $project_time->users()->sync($request->users);

        if($request->generate_presence_list) {
            if($request->signing_boss) {
                $request->session()->put('signing_boss', $request->signing_boss);
            }

            \Session::flash('download_file', '/projecttime/presence_list/'.$project_time->id);
            return redirect($request->return_url)->with('success', __('Projekttiden har registrerats och närvarolista laddas ner'));
        } else {
            return redirect($request->return_url)->with('success', __('Projekttiden har registrerats'));
        }
    }

    public function index() {

        $workplaces = null;

        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all();
        } elseif (Auth::user()->hasRole('Arbetsplatsadministratör')) {
            $workplaces = Auth::user()->admin_workplaces; //->prepend(Auth::user()->workplace);
        }

        //if(ClosedMonth::where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->exists()) {
        //    $mindate = date("Y-m")."-01";
        //} else {
            $mindate = date("Y-m", strtotime("first day of previous month"))."-01";
        //}

        $data = [
            'workplaces' => $workplaces,
            'mindate' => $mindate,
        ];

        return view('projecttime.index')->with($data);
    }

    public function edit(ProjectTime $project_time) {
        $project_time_types = ProjectTimeType::all();
        $user = Auth::user();

        if($project_time->registered_by_user != $user && ! $user->hasRole('Admin') && ! $project_time->workplace->workplace_admins->contains('id', $user->id)) {
            abort(403);
        }

        //If last month is already attested, no further time may be registered on it, so start the calendar picker on first day of this month
        //The same goes if last month is closed by administrator
        //if($user->time_attests->where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->isNotEmpty() ||
        //   ClosedMonth::where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->exists()) {
        //    $mindate = date("Y-m")."-01";
        //} else {
            $mindate = date("Y-m", strtotime("first day of previous month"))."-01";
        //}

        $data = [
            'project_time_types' => $project_time_types,
            'user' => $user,
            'workplace' => $project_time->workplace,
            'mindate' => $mindate,
            'project_time' => $project_time,
        ];
        return view('projecttime.edit')->with($data);
    }

    public function update(Request $request, ProjectTime $project_time) {
        $user = Auth::user();
        if($project_time->registered_by_user != $user && ! $user->hasRole('Admin') && ! $project_time->workplace->workplace_admins->contains('id', $user->id)) {
            abort(403);
        }

        usleep(50000);
        $request->validate([
            'starttime' => 'required|date_format:H:i',
            'endtime' => 'required|date_format:H:i|after:starttime',
            'date' => 'required|date|date_format:Y-m-d',
            'users' => 'required',
            'workplace_id' => 'required',
        ],
        [
            'starttime.required' => __('Du måste ange en starttid!'),
            'endtime.required' => __('Du måste ange en sluttid!'),
            'date.required' => __('Du måste ange ett datum!'),
            'date.date' => __('Datumet måste vara i formatet yyyy-mm-dd!'),
            'date.date_format' => __('Datumet måste vara i formatet yyyy-mm-dd!'),
            'starttime.date_format' => __('Tidpunkterna måste vara i formatet hh:mm!'),
            'endtime.date_format' => __('Tidpunkterna måste vara i formatet hh:mm!'),
            'users.required' => __('Du måste ange minst en användare som tid ska registreras på!'),
            'endtime.after' => __('Sluttiden får inte inträffa före starttiden!'),
        ]);

        $workplace = Workplace::find($request->workplace_id);

        $project_time->date = $request->date;
        $project_time->starttime = $request->starttime;
        $project_time->endtime = $request->endtime;
        $project_time->workplace_id = $workplace->id;
        $project_time->project_time_type_id = $request->type;
        $project_time->registered_by = Auth::user()->id;
        $project_time->save();
        $project_time->users()->sync($request->users);

        return redirect('/projecttime')->with('success', __('Projekttiden har ändrats'));
    }

    public function presence_list(Request $request, ProjectTime $project_time) {
        $signing_boss = User::find($request->session()->pull('signing_boss'));
        $data = [
            'project_time' => $project_time,
            'signing_boss' => $signing_boss,
        ];

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('projecttime.presence_list', $data);

        return $pdf->download(__('Evikomp närvarolista.pdf'));
    }

}
