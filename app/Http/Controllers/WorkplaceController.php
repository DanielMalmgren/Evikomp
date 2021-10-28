<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Workplace;
use App\Track;
use App\WorkplaceType;
use App\Municipality;

class WorkplaceController extends Controller
{
    public function create() {
        $tracks = Track::all();
        $workplace_types = WorkplaceType::all();
        $data = [
            'municipalities' => Municipality::orderBy('name')->get(),
            'tracks' => $tracks,
            'workplace_types' => $workplace_types,
        ];
        return view('workplaces.create')->with($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'workplace_type' => 'required',
            'municipality' => 'required',
        ]);

        $workplace = new Workplace();
        $workplace->name = $request->name;
        $workplace->workplace_type_id = $request->workplace_type;
        $workplace->municipality_id = $request->municipality;
        $workplace->includetimeinreports = $request->includetimeinreports;
        $workplace->send_attest_reminders = $request->send_attest_reminders;
        $workplace->training_coordinator = $request->training_coordinator;
        $workplace->save();

        logger("Workplace ".$workplace->name." is being created by ".Auth::user()->name);
        activity()->on($workplace)->log('created');

        return $this->update($request, $workplace);
    }

    public function edit() {
        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all()->sortBy('name');
        } else {
            $workplaces = Auth::user()->admin_workplaces;
        }
        $tracks = Track::where('active', 1)->get();
        $workplace_types = WorkplaceType::all();
        $data = [
            'workplaces' => $workplaces,
            'tracks' => $tracks,
            'workplace_types' => $workplace_types,
        ];

        return view('workplaces.edit')->with($data);
    }

    public function ajax(Workplace $workplace) {
        $tracks = Track::where('active', 1)->get();
        $workplace_types = WorkplaceType::all();
        $data = [
            'workplace' => $workplace,
            'tracks' => $tracks,
            'workplace_types' => $workplace_types,
            'deleteable' => $workplace->users->isEmpty() && $workplace->workplace_admins->isEmpty(),
        ];
        return view('workplaces.ajax')->with($data);
    }

    public function update(Request $request, Workplace $workplace) {
        $this->validate($request, [
            'workplace_type' => 'required',
        ]);

        if($request->adminlevel) {
            foreach($request->adminlevel as $user_id => $adminlevel) {
                $user = User::find($user_id);
                if(isset($user)) {
                    logger('Ger '.$user->name.' adminbehörighet på nivå '.$adminlevel.' på '.$workplace->name);

                    $user->admin_workplaces()->detach($workplace); //Detach first in case user already has another level role
                    $user->admin_workplaces()->attach($workplace, ['attestlevel'=>$adminlevel]);
                    $user->assignRole('Arbetsplatsadministratör');
                }
            }
        }

        if($request->remove_admin) {
            foreach(array_keys($request->remove_admin) as $user_id) {
                $user = User::find($user_id);
                logger('Tar bort '.$user->name.' ifrån adminbehörighet på '.$workplace->name);
                $user->admin_workplaces()->detach($workplace);
                if($user->admin_workplaces()->count() === 0) {
                    $user->removeRole('Arbetsplatsadministratör');
                }
            }
        }

        $workplace->tracks()->sync($request->tracks);
        $workplace->name = $request->name;
        $workplace->workplace_type_id = $request->workplace_type;
        $workplace->includetimeinreports = $request->includetimeinreports;
        $workplace->send_attest_reminders = $request->send_attest_reminders;
        $workplace->training_coordinator = $request->training_coordinator;
        $workplace->save();

        logger("Workplace ".$workplace->name." is being edited by ".Auth::user()->name);
        activity()->on($workplace)->log('updated');

        return redirect('/workplace')->with('success', 'Uppgifterna sparade');
    }

    public function getusers(Workplace $workplace) {
        $users = $workplace->users->map->only(['id', 'name'])->all();
        return response()->json(['users' => $users]);
    }

    public function destroy(Workplace $workplace) {

        logger("Workplace ".$workplace->name." is being removed by ".Auth::user()->name);

        if(! $workplace->project_times->isEmpty()) {
            logger('Relocating registered project time for '.$workplace->name);
            foreach($workplace->project_times as $project_time) {
                logger("Relocating project time on ".$project_time->date);
                $alternative_workplace = $project_time->users->first()->workplace;
                if(isset($alternative_workplace)) {
                    logger("Relocating project time to ".$alternative_workplace->name);
                    $project_time->workplace_id = $alternative_workplace->id;
                    $project_time->save();
                } else {
                    logger("Project time not relocatable, removing!");
                    $project_time->delete();
                }
            }
        }
        logger('Destroying workplace '.$workplace->name);
        activity()->on($workplace)->log('deleted');

        $workplace->delete();
    }
}
