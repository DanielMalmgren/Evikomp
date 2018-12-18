<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Municipality;
use App\Workplace;
use App\User;
use App\Locale;

class FirstLoginController extends Controller
{
    public function show(Request $request) {
        $data = array(
            'municipalities' => Municipality::orderBy('name')->get(),
            'workplaces' => Workplace::orderBy('name')->get(),
            'locales' => Locale::All(),
            'user' => Auth::user()
        );
        return view('pages.firstlogin')->with($data);
    }

    public function storeLanguage(Request $request) {

        $this->validate($request, [
            'locale' => 'required'
        ]);

        $user = $request->user();
        $user->locale_id = $request->input('locale');
        $user->save();

        return redirect('/');
    }

    public function store(Request $request) {

        //logger(print_r($request->all(), true));

        $this->validate($request, [
            'workplace' => 'required',
            'email' => 'required|email'
        ]);

        $user = $request->user();
        $user->email = $request->input('email');
        $user->workplace_id = $request->input('workplace');
        $user->save();

        $user->assignRole('Registered');

        return redirect('/')->with('success', 'Uppgifterna sparade');
    }
}
