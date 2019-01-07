<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Locale;

class SettingsController extends Controller
{
    public function edit() {
        $data = array(
            'user' => Auth::user(),
            'locales' => Locale::All()
        );

        return view('pages.settings')->with($data);
    }

    public function store(Request $request) {

        //logger(print_r($request->all(), true));

        $this->validate($request, [
            'locale' => 'required'
        ]);

        $user = $request->user();
        $user->locale_id = $request->input('locale');
        $user->save();

        return redirect('/')->with('success', 'Uppgifterna sparade');
    }
}