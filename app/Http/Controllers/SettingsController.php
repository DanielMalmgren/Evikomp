<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Locale;
use App\Track;
use App\Municipality;
use App\Workplace;
use App\User;

class SettingsController extends Controller
{
    public function edit() {
        $tracks = Track::all();

        $data = array(
            'municipalities' => Municipality::orderBy('name')->get(),
            'workplaces' => Workplace::orderBy('name')->get(),
            'user' => Auth::user(),
            'locales' => Locale::All(),
            'tracks' => $tracks
        );

        return view('pages.settings')->with($data);
    }

    public function storeLanguage(Request $request) {

        $this->validate($request, [
            'locale' => 'required'
        ]);

        $user = $request->user();
        $user->locale_id = $request->input('locale');
        $user->save();

        return redirect('/settings');
    }

    public function store(Request $request) {

        //logger(print_r($request->all(), true));

        $this->validate($request, [
            'workplace' => 'required',
            'email' => 'required|email'
        ]);

        $user = $request->user();
        $user->tracks()->sync($request->tracks);
        $user->email = $request->input('email');
        $user->workplace_id = $request->input('workplace');
        $user->locale_id = $request->input('locale');
        $user->save();

        $user->assignRole('Registered');

        return redirect('/')->with('success', 'Uppgifterna sparade');
    }
}
