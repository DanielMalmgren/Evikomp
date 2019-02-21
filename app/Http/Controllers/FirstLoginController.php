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
        if(Auth::user()->accepted_gdpr) {
            return redirect('/settings');
        } else {
            return view('pages.gdprinfo')->with($data);
        }
    }

    public function storeLanguage(Request $request) {

        $this->validate($request, [
            'locale' => 'required'
        ]);

        $user = $request->user();
        $user->locale_id = $request->input('locale');
        $user->save();

        return redirect('/firstlogin');
    }

    public function storeGdprAccept(Request $request) {
        $user = $request->user();
        $user->accepted_gdpr = true;
        $user->save();

        return redirect('/settings');
    }
}
