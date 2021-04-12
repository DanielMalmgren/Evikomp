<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Marwelln\Holiday;
use App\User;
use App\Workplace;
use App\ProjectTime;
use App\ProjectTimeType;
use App\ClosedMonth;
use App\Track;
use App\Lesson;
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

    public function create(Request $request) {
        if($request->allDay == 'true') {
            $date = $request->date;
            $time = null;
        } else {
            $date = substr($request->date, 0, 10);
            $time = substr($request->date, 11, 5);
        }

        $project_time_types = ProjectTimeType::all();
        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all()->sortBy('name');
        } else {
            $workplaces = Auth::user()->admin_workplaces->sortBy('name');
        }

        $mindate = date("Y-m-d", strtotime("first day of previous month"));
        $maxdate = date("Y-m-d", strtotime("last day of next month"));

        $data = [
            'workplaces' => $workplaces,
            'project_time_types' => $project_time_types,
            'mindate' => $mindate,
            'maxdate' => $maxdate,
            'date' => $date,
            'time' => $time,
            'allDay' => $request->allDay,
            'tracks' => Track::where('active', true)->get(),
            'lessons' => Lesson::where('active', true)->get(),
        ];
        if(!isset($request->date)) {
            \Session::flash('warning', __('På grund av förändringar i plattformen kommer detta menyval att försvinna inom kort.<br> Registrera istället tid i projektet genom att gå in på menyvalet <i><b>Administration->Hantera lärtillfällen</b></i> och klicka på ett datum i kalendern!'));
        }
        return view('projecttime.create')->with($data);
    }

    public function createsingleuser() {
        $project_time_types = ProjectTimeType::all();
        $user = Auth::user();

        $mindate = date("Y-m-d", strtotime("first day of previous month"));

        $data = [
            'project_time_types' => $project_time_types,
            'user' => $user,
            'workplace' => $user->workplace,
            'mindate' => $mindate,
        ];
        \Session::flash('warning', __('På grund av förändringar i plattformen kommer detta menyval att försvinna inom kort.<br> Registrera istället tid i projektet genom att gå in på menyvalet <i><b>Administration->Hantera lärtillfällen</b></i> och klicka på ett datum i kalendern!'));
        return view('projecttime.createsingleuser')->with($data);
    }

    public function ajax(Request $request, Workplace $workplace=null) {
        $project_time_types = ProjectTimeType::all();

        $mindate = date("Y-m-d", strtotime("first day of previous month"));
        $maxdate = date("Y-m-d", strtotime("last day of next month"));

        if($workplace === null) {
            $workplace = Auth::user()->workplace;
        }

        $data = [
            'workplace' => $workplace,
            'project_time_types' => $project_time_types,
            'mindate' => $mindate,
            'maxdate' => $maxdate,
            'date' => $request->date,
            'time' => $request->time,
            'allDay' => $request->allDay,
            'tracks' => Track::where('active', true)->get(),
            'lessons' => Lesson::where('active', true)->get(),
        ];
        return view('projecttime.ajax')->with($data);
    }

    public function destroy(ProjectTime $project_time) {
        logger('Destroying project time '.$project_time->id);
        $project_time->delete();
    }

    public function store(Request $request) {
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

        $year = substr($request->date, 0, 4);
        $month = substr($request->date, 5, 2);

        //Loopa igenom alla de aktuella användarna
        //Ta fram deras tidsregistreringar för den aktuella dagen
        //För varje registrering, kolla så inte startdate eller enddate är mellan registrerigens start eller slut, kolla även tvärtemot (Så inte registreringen ligger inom vårt intervall)
        foreach($request->users as $user_id) {
            $user = User::find($user_id);

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
        $project_time->need_teacher = $request->need_teacher;
        $project_time->save();
        $project_time->users()->sync($request->users);
        $project_time->lessons()->sync($request->lessons);

        if($request->generate_presence_list) {
            if($request->signing_boss) {
                $request->session()->put('signing_boss', $request->signing_boss);
            }

            \Session::flash('download_file', '/projecttime/presence_list/'.$project_time->id);
            return redirect("/projecttime")->with('success', __('Lärtillfället har registrerats och närvarolista laddas ner'));
        } else {
            return redirect("/projecttime")->with('success', __('Lärtillfället har registrerats'));
        }
    }

    public function index(Request $request) {
        $mindate = date("Y-m-d", strtotime("first day of previous month"));
        $maxdate = date("Y-m-d", strtotime("last day of next month"));

        $events = [];

        //Make sundays red
        $events[] = \Calendar::event(
            '', //event title
            true, //full day event?
            $mindate,
            $maxdate,
            0,
            [
                'display' => 'background',
                'color' => '#ff0000',
                'daysOfWeek' => '0',
            ]
        );

        $swedish_holiday_names = [
            'newyearsday' => 'Nyårsdagen',
            'newyearseve' => 'Nyårsafton',
            'epiphany' => 'Trettondedag jul',
            'easter' => 'Påskdagen',
            'goodfriday' => 'Långfredagen',
            'eastermonday' => 'Annandag påsk',
            'ascensionday' => 'Kristi himmelfärdsdag',
            'pentecostday' => 'Pingstdagen',
            'mayday' => 'Första maj',
            'swedishnationalday' => 'Sveriges nationaldag',
            'midsummereve' => 'Midsommarafton',
            'midsummerday' => 'Midsommardagen',
            'allsaintsday' => 'Alla helgons dag',
            'christmaseve' => 'Julafton',
            'christmasday' => 'Juldagen',
            'boxingday' => 'Annandag jul'
        ];

        //Make Swedish holidays red
        $holidays = (new Holiday)->between(new \DateTime($mindate), new \DateTime($maxdate));
        foreach($holidays as $holiday) {
            $events[] = \Calendar::event(
                $swedish_holiday_names[$holiday['id']], //event title
                true, //full day event?
                $holiday['date'],
                $holiday['date'],
                0,
                [
                    'display' => 'background',
                    'color' => '#ff0000'
                ]
            );
        }

        if(Auth::user()->hasRole('Admin')) {
            $project_times = ProjectTime::where('date', '>=', $mindate)
                ->with(['workplace'])
                ->orderBy('date')
                ->orderBy('starttime')
                ->get();
        } elseif (Auth::user()->hasRole('Arbetsplatsadministratör')) {
            $workplaces = Auth::user()->admin_workplaces->pluck('id');
            $project_times = ProjectTime::whereIn('workplace_id', $workplaces)
                ->orWhereIn('training_coordinator_id', $workplaces)
                ->where('date', '>=', $mindate)
                ->with(['workplace'])
                ->orderBy('date')
                ->orderBy('starttime')
                ->get();
        } else {
            $project_times = ProjectTime::join('project_time_user', 'project_times.id', '=', 'project_time_user.project_time_id')
                ->where('teacher_id', Auth::user()->id)
                ->orWhere('project_time_user.user_id', Auth::user()->id)
                ->where('date', '>=', $mindate)
                ->with(['workplace'])
                ->orderBy('date')
                ->orderBy('starttime')
                ->get();
        }

        $tzstring = (new \DateTime('now', new \DateTimeZone( 'Europe/Stockholm' )))->format('P');
        foreach($project_times as $project_time) {
            $events[] = \Calendar::event(
                $project_time->workplace->name, //event title
                false, //full day event?
                $project_time->date.'T'.$project_time->startstr().':00'.$tzstring,
                $project_time->date.'T'.$project_time->endstr().':00'.$tzstring,
                $project_time->id,
                [
                    'url' => '/projecttime/'.$project_time->id.'/edit',
                    'backgroundColor' => $project_time->color(),
                ]
            );
        }

        $calendar = \Calendar::addEvents($events)
                ->setOptions([
                    'locale' => substr(\App::getLocale(), 0, 2),
                    'timezone' => 'Europe/Stockholm',
                    'themeSystem' => 'bootstrap',
                    'weekNumberCalculation' => 'ISO',
                    'weekNumbers' => true,
                    'displayEventTime' => true,
                    'displayEventEnd' => true,
                    'eventTimeFormat' => [
                        'hour' => '2-digit',
                        'minute' => '2-digit',
                        'meridiem' => false
                    ],
                    'selectable' => true,
                    'initialView' => 'dayGridMonth',
                    'slotMinTime' => "07:00:00",
                    'slotMaxTime' => "22:00:00",
                    'validRange' => [
                        'start' => $mindate,
                        'end' => $maxdate
                    ],
                    'headerToolbar' => [
                        'end' => 'today prev,next dayGridMonth workMonth timeGridWeek workWeek'
                    ],
                    'views' => [
                        'workMonth' => [
                            'buttonText' => __('Månad utan helger'),
                            'type' => 'dayGridMonth',
                            'weekends' => false,
                        ],
                        'workWeek' => [
                            'buttonText' => __('Arbetsvecka'),
                            'type' => 'timeGridWeek',
                            'weekends' => false,
                        ],
                    ],
                ]);

        $calendar->setCallbacks([
                    'dateClick' => 'function(dateClickInfo){onDateClick(dateClickInfo)}'
                ]);

        $data = [
            'project_times' => $project_times,
            'mindate' => $mindate,
            'calendar' => $calendar,
        ];

        return view('projecttime.index')->with($data);
    }

    public function edit(ProjectTime $project_time) {
        $project_time_types = ProjectTimeType::all();
        $user = Auth::user();

        if($project_time->teacher_id !== null) {
            $teacher_assigned = true;
        } else {
            $teacher_assigned = false;
        }

        $can_edit = false;
        $can_see_contact_info = false;
        $can_see_collegues = false;
        $can_change_training_coordinator = false;
        $can_change_teacher = false;

        if($user->hasRole('Admin')){
            $can_edit = true;
            $can_see_contact_info = true;
            $can_see_collegues = true;
            $can_change_training_coordinator = true;
            $can_change_teacher = true;
            $teacher_assigned = false;
        } elseif($project_time->workplace->workplace_admins->contains('id', $user->id)) {
            $can_edit = true;
            $can_see_contact_info = true;
            $can_see_collegues = true;
        } elseif(isset($project_time->training_coordinator) && 
                ($project_time->training_coordinator->users->contains('id', $user->id) ||
                $project_time->training_coordinator->workplace_admins->contains('id', $user->id ||
                $project_time->teacher_id == $user->id))) {
            $can_edit = false;
            $can_see_contact_info = true;
            $can_see_collegues = true;
            $can_change_teacher = true;
        } elseif($project_time->registered_by == $user->id) {
            $can_edit = true;
        } elseif($project_time->users->contains('id', $user->id)) {
            $can_see_collegues = true;
        } else {
            abort(403);
        }

        $mindate = date("Y-m-d", strtotime("first day of previous month"));
        $maxdate = date("Y-m-d", strtotime("last day of next month"));

        $data = [
            'project_time_types' => $project_time_types,
            'user' => $user,
            'workplace' => $project_time->workplace,
            'mindate' => $mindate,
            'maxdate' => $maxdate,
            'project_time' => $project_time,
            'training_coordinators' => Workplace::where('training_coordinator', true)->get(),
            'teachers' => isset($project_time->training_coordinator_id)?$project_time->training_coordinator->users:null,
            'tracks' => Track::where('active', true)->get(),
            'lessons' => Lesson::where('active', true)->get(),
            'can_edit' => $can_edit,
            'can_see_contact_info' => $can_see_contact_info,
            'can_see_collegues' => $can_see_collegues,
            'can_change_training_coordinator' => $can_change_training_coordinator,
            'can_change_teacher' => $can_change_teacher,
            'teacher_assigned' => $teacher_assigned,
        ];
        return view('projecttime.edit')->with($data);
    }

    public function attest_from_list(ProjectTime $project_time) {
        $user = Auth::user();

        if(!$user->hasRole('Admin') && ! $project_time->workplace->workplace_admins->contains('id', $user->id)) {
            abort(403);
        }

        $data = [
            'user' => $user,
            'project_time' => $project_time,
        ];
        return view('projecttime.attest_from_list')->with($data);
    }

    public function update(Request $request, ProjectTime $project_time) {
        usleep(50000);
        $user = Auth::user();

        if($user->hasRole('Admin') ||
            $project_time->workplace->workplace_admins->contains('id', $user->id)) {
            $request->validate([
                'starttime' => 'date_format:H:i',
                'endtime' => 'date_format:H:i|after:starttime',
                'date' => 'date|date_format:Y-m-d',
                'users' => 'required',
            ],
            [
                'date.date' => __('Datumet måste vara i formatet yyyy-mm-dd!'),
                'date.date_format' => __('Datumet måste vara i formatet yyyy-mm-dd!'),
                'starttime.date_format' => __('Tidpunkterna måste vara i formatet hh:mm!'),
                'endtime.date_format' => __('Tidpunkterna måste vara i formatet hh:mm!'),
                'users.required' => __('Du måste ange minst en användare som tid ska registreras på!'),
                'endtime.after' => __('Sluttiden får inte inträffa före starttiden!'),
            ]);
    
            if(isset($request->date)) {
                $project_time->date = $request->date;
            }
            if(isset($request->starttime)) {
                $project_time->starttime = $request->starttime;
            }
            if(isset($request->endtime)) {
                $project_time->endtime = $request->endtime;
            }
            $project_time->project_time_type_id = $request->type;
            $project_time->need_teacher = $project_time->need_teacher || $request->need_teacher;
            if(isset($request->training_coordinator)) {
                $project_time->training_coordinator_id = $request->training_coordinator;
            }
            if(isset($request->teacher)) {
                $project_time->teacher_id = $request->teacher;
            }
            $project_time->save();
            $project_time->users()->sync($request->users);
            $project_time->lessons()->sync($request->lessons);
        } elseif(isset($project_time->training_coordinator) && 
                $project_time->training_coordinator->users->contains('id', $user->id) ||
                $project_time->training_coordinator->workplace_admins->contains('id', $user->id)) {
            if(isset($request->teacher)) {
                $project_time->teacher_id = $request->teacher;
            }
            $project_time->save();
        } elseif($project_time->registered_by == $user->id) {
            $request->validate([
                'starttime' => 'date_format:H:i',
                'endtime' => 'date_format:H:i|after:starttime',
                'date' => 'date|date_format:Y-m-d',
                'users' => 'required',
            ],
            [
                'date.date' => __('Datumet måste vara i formatet yyyy-mm-dd!'),
                'date.date_format' => __('Datumet måste vara i formatet yyyy-mm-dd!'),
                'starttime.date_format' => __('Tidpunkterna måste vara i formatet hh:mm!'),
                'endtime.date_format' => __('Tidpunkterna måste vara i formatet hh:mm!'),
                'users.required' => __('Du måste ange minst en användare som tid ska registreras på!'),
                'endtime.after' => __('Sluttiden får inte inträffa före starttiden!'),
            ]);
    
            $project_time->date = $request->date;
            $project_time->starttime = $request->starttime;
            $project_time->endtime = $request->endtime;
            $project_time->project_time_type_id = $request->type;
            $project_time->save();
        } else {
            abort(403);
        }

        return redirect('/projecttime')->with('success', __('Lärtillfället har ändrats'));
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
