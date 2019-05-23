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
        $workplace->save();

        return $this->update($request, $workplace);
    }

    public function edit() {
        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all()->sortBy('name');
        } else {
            $workplaces = Auth::user()->admin_workplaces;
        }
        $tracks = Track::all();
        $workplace_types = WorkplaceType::all();
        $data = [
            'workplaces' => $workplaces,
            'tracks' => $tracks,
            'workplace_types' => $workplace_types,
        ];

        return view('workplaces.edit')->with($data);
    }

    public function ajax(Workplace $workplace) {
        $tracks = Track::all();
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
        $workplace->save();

        return redirect('/workplace')->with('success', 'Uppgifterna sparade');
    }

    public function destroy(Workplace $workplace) {
        if(! $workplace->project_times->isEmpty()) {
            logger('Relocating registered project time for '.$workplace->name);
            foreach($workplace->project_times as $project_time) {
                logger("Relocating project time on ".$project_time->date);
                $alternative_workplace = $project_time->users->first()->workplace;
                logger("Relocating project time to ".$alternative_workplace->name);
                $project_time->workplace_id = $alternative_workplace->id;
                $project_time->save();
            }
        }
        logger('Destroying workplace '.$workplace->name);
        $workplace->delete();
    }
}
