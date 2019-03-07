<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Workplace;
use App\ProjectTime;
use App\ProjectTimeType;
use App\Http\Requests\StoreProjectTime;

class ProjectTimeController extends Controller
{
    public function create(Request $request) {
        $project_time_types = ProjectTimeType::all();
        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all();
        } else {
            $workplaces = Auth::user()->admin_workplaces;
        }

        $data = array(
            'workplaces' => $workplaces,
            'project_time_types' => $project_time_types
        );
        return view('projecttime.create')->with($data);
    }

    public function createsingleuser(Request $request) {
        $project_time_types = ProjectTimeType::all();

        $data = array(
            'project_time_types' => $project_time_types,
            'user' => Auth::user(),
            'workplace' => Auth::user()->workplace
        );
        return view('projecttime.createsingleuser')->with($data);
    }

    public function ajax(Workplace $workplace) {
        $project_time_types = ProjectTimeType::all();
        $data = array(
            'workplace' => $workplace,
            'project_time_types' => $project_time_types
        );
        return view('projecttime.ajax')->with($data);
    }

    public function store(Request $request, Workplace $workplace) {
        usleep(50000);
        $request->validate(['starttime' => 'required',
                            'endtime' => 'required|after:starttime',
                            'date' => 'required',
                            'workplace_id' => 'required'],
                            ['starttime.required' => __('Du måste ange en starttid!'),
                            'endtime.required' => __('Du måste ange en sluttid!'),
                            'date.required' => __('Du måste ange ett datum!'),
                            'endtime.after' => __('Sluttiden får inte inträffa före starttiden!')]);

        //Loopa igenom alla de aktuella användarna
        //Ta fram deras tidsregistreringar för den aktuella dagen
        //För varje registrering, kolla så inte startdate eller enddate är mellan registrerigens start eller slut, kolla även tvärtemot (Så inte registreringen ligger inom vårt intervall)
        foreach($request->users as $user_id) {
            $user = User::find($user_id);
            logger("Kollar tidskrock för ".$user->name);
            $occasions = $user->project_times()->where('date', $request->date)->get();
            foreach($occasions as $occasion) {
                logger("Kollar tillfället mellan ".$occasion->startstr()." och ".$occasion->endstr());
                if(($request->starttime > $occasion->startstr() && $request->starttime < $occasion->endstr()) ||
                   ($request->endtime > $occasion->startstr() && $request->endtime < $occasion->endstr()) ||
                   ($occasion->starttime > $request->starttime && $occasion->startstr() < $request->endtime))  {
                    return back()->with('error', $user->name.' har redan ett tillfälle inlagt mellan '.$occasion->startstr().' och '.$occasion->endstr().'!')->withInput();
                }
            }
        }
        //TODO: Loopa även igenom alla attesteringar som skulle kunna vara aktuella! Man kan inte lägga in tid på en månad som redan är attesterad!

        $project_time = new ProjectTime;
        $project_time->date = $request->date;
        $project_time->starttime = $request->starttime;
        $project_time->endtime = $request->endtime;
        $project_time->workplace_id = $workplace->id;
        $project_time->project_time_type_id = $request->type;
        $project_time->save();
        $project_time->users()->sync($request->users);

        return redirect('/')->with('success', 'Projekttiden har registrerats');
    }

}
