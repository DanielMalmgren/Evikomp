<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Workplace;
use App\Track;

class WorkplaceSettingsController extends Controller
{
    public function edit() {
        $workplaces = Auth::user()->admin_workplaces;
        $tracks = Track::all();
        $data = array(
            'workplaces' => $workplaces,
            'tracks' => $tracks
        );

        return view('pages.workplacesettings')->with($data);
    }

    public function ajax($workplace_id) {
        $workplace = Workplace::find($workplace_id);
        $tracks = Track::all();
        $data = array(
            'workplace' => $workplace,
            'tracks' => $tracks
        );
        return view('ajax.workplacesettings')->with($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'tracks' => 'required',
            'workplace_id' => 'required'
        ]);

        $workplace = Workplace::find($request->workplace_id);

        $workplace->tracks()->sync($request->tracks);

        return redirect('/wpsettings')->with('success', 'Uppgifterna sparade');
    }
}
