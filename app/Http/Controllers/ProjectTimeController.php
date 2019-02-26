<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Workplace;
use App\ProjectTime;
use App\ProjectTimeType;
use App\Http\Requests\StoreProjectTime;

class ProjectTimeController extends Controller
{
    public function create(Request $request) {
        $workplaces = Workplace::all();
        $data = array(
            'workplaces' => $workplaces
        );
        return view('projecttime.create')->with($data);
    }

    public function ajax(Workplace $workplace) {
        $project_time_types = ProjectTimeType::all();
        $data = array(
            'workplace' => $workplace,
            'project_time_types' => $project_time_types
        );
        return view('projecttime.ajax')->with($data);
    }

    public function store(StoreProjectTime $request, Workplace $workplace) {
        //$workplace = Workplace::find($request->workplace_id);

        //$parsedtime = date_parse($request->time);
        //$minutes = date_parse($request->time)['hour']*60 + date_parse($request->time)['minute'];
        //logger("Registrerar en lektion för ".$workplace->name.", antal minuter: ".$minutes);

        $project_time = new ProjectTime;
        $project_time->date = $request->date;
        $project_time->starttime = $request->starttime;
        $project_time->endtime = $request->endtime;
        $project_time->workplace_id = $workplace->id;
        $project_time->project_time_type_id = $request->type;
        $project_time->save();
        $project_time->users()->sync($request->users);

        //$project_time->minutes();

        //TODO: Fixa koll så man inte försöker lägga in överlappande tid på samma arbetsplats! Kanske ska ligga i StoreProjectTime?

        return redirect('/projecttime/create')->with('success', 'Projekttiden har registrerats');
    }

}
