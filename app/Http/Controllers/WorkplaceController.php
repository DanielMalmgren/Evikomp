<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Workplace;
use App\Track;
use App\WorkplaceType;
use App\Municipality;

class WorkplaceController extends Controller
{
    public function create(Request $request) {
        $tracks = Track::all();
        $workplace_types = WorkplaceType::all();
        $data = array(
            'municipalities' => Municipality::orderBy('name')->get(),
            'tracks' => $tracks,
            'workplace_types' => $workplace_types
        );
        return view('workplaces.create')->with($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'workplace_type' => 'required',
            'municipality' => 'required'
        ]);

        $workplace = new Workplace;
        $workplace->name = $request->name;
        $workplace->workplace_type_id = $request->workplace_type;
        $workplace->municipality_id = $request->municipality;
        $workplace->save();
        $workplace->tracks()->sync($request->tracks);

        return redirect('/workplace')->with('success', 'Uppgifterna sparade');
    }

    public function edit() {
        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all();
        } else {
            $workplaces = Auth::user()->admin_workplaces;
        }
        $tracks = Track::all();
        $workplace_types = WorkplaceType::all();
        $data = array(
            'workplaces' => $workplaces,
            'tracks' => $tracks,
            'workplace_types' => $workplace_types
        );

        return view('workplaces.edit')->with($data);
    }

    public function ajax(Workplace $workplace) {
        $tracks = Track::all();
        $workplace_types = WorkplaceType::all();
        $data = array(
            'workplace' => $workplace,
            'tracks' => $tracks,
            'workplace_types' => $workplace_types
        );
        return view('workplaces.ajax')->with($data);
    }

    public function update(Request $request, Workplace $workplace) {
        $this->validate($request, [
            'tracks' => 'required',
            'workplace_type' => 'required'
        ]);

        $workplace->tracks()->sync($request->tracks);
        $workplace->workplace_type_id = $request->workplace_type;
        $workplace->save();

        return redirect('/workplace')->with('success', 'Uppgifterna sparade');
    }
}
