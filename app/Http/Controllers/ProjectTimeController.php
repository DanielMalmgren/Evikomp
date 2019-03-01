<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->validate(
            [
                'starttime' => 'required',
                'endtime' => 'required|after:starttime',
                'date' => 'required',
                'workplace_id' => 'required'
            ],
            [
            'starttime.required' => __('Du måste ange en starttid!'),
            'endtime.required' => __('Du måste ange en sluttid!'),
            'date.required' => __('Du måste ange ett datum!'),
            'endtime.after' => __('Sluttiden får inte inträffa före starttiden!')
        ]);

        $project_time = new ProjectTime;
        $project_time->date = $request->date;
        $project_time->starttime = $request->starttime;
        $project_time->endtime = $request->endtime;
        $project_time->workplace_id = $workplace->id;
        $project_time->project_time_type_id = $request->type;
        $project_time->save();
        $project_time->users()->sync($request->users);

        //TODO: Fixa koll så man inte försöker lägga in överlappande tid på samma arbetsplats/person! Kanske ska ligga i StoreProjectTime?

        return redirect('/')->with('success', 'Projekttiden har registrerats');
    }

}
