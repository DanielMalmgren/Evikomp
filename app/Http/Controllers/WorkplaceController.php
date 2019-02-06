<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Workplace;
use App\Track;
use App\WorkplaceType;

class WorkplaceController extends Controller
{
    public function edit() {
        $workplaces = Auth::user()->admin_workplaces;
        $tracks = Track::all();
        $data = array(
            'workplaces' => $workplaces,
            'tracks' => $tracks
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

    public function store(Request $request) {
        $this->validate($request, [
            'tracks' => 'required',
            'workplace_id' => 'required',
            'workplace_type' => 'required'
        ]);

        $workplace = Workplace::find($request->workplace_id);

        $workplace->tracks()->sync($request->tracks);

        $workplace->workplace_type_id = $request->workplace_type;
        $workplace->save();

        return redirect('/workplace')->with('success', 'Uppgifterna sparade');
    }
}
