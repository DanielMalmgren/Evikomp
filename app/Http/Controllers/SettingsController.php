<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Locale;
use App\Track;
use App\Municipality;
use App\Workplace;
use App\User;
use App\Title;

class SettingsController extends Controller
{
    public function edit() {
        $tracks = Track::all();

        $data = array(
            'municipalities' => Municipality::orderBy('name')->get(),
            'workplaces' => Workplace::orderBy('name')->get(),
            'user' => Auth::user(),
            'locales' => Locale::All(),
            'titles' => Title::All(),
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
        usleep(50000);
        $this->validate($request, [
            'workplace' => 'required',
            'email' => 'required|email|unique:users,email,'.Auth::user()->id,
            'mobile' => 'required',
            'title' => 'required',
            'terms_of_employment' => 'required',
            'full_or_part_time' => 'required'
            ],
            ['workplace.required' => __('Du måste ange din arbetsplats!'),
            'terms_of_employment.required' => __('Du måste ange anställningsvillkor!'),
            'full_or_part_time.required' => __('Du måste ange anställningens omfattning!'),
            'email.required' => __('Du måste ange din e-postadress!'),
            'email.unique' => __('E-postadressen du har angett finns registrerad på en annan användare!'),
            'mobile.required' => __('Du måste ange ditt mobilnummer!'),
            'title.required' => __('Du måste ange din befattning!'),
            'email.email' => __('vänligen ange en giltig e-postadress!')]);

        $user = $request->user();
        $user->tracks()->sync($request->tracks);
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        if($request->input('firstname')) {
            $user->firstname = $request->input('firstname');
        } else {
            $user->firstname = $user->saml_firstname;
        }
        $user->title_id = $request->input('title');
        $user->terms_of_employment = $request->input('terms_of_employment');
        $user->full_or_part_time = $request->input('full_or_part_time');
        $user->workplace_id = $request->input('workplace');
        $user->save();

        $user->assignRole('Registrerad');

        return redirect('/')->with('success', __('Inställningarna sparade'));
    }
}
