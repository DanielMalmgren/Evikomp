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
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function edit(?User $user = null): View {
        if(! $user) {
            $user = Auth::user();
        }

        if($user->isNot(Auth::user()) && ! Auth::user()->hasRole('Admin') && (! isset($user->workplace) || ! $user->workplace->workplace_admins->contains('id', Auth::user()->id))) {
            abort(403);
        }

        if($user->can('list all lessons')) {
            $tracks = Track::orderBy('order')->get();
        } else {
            $tracks = Track::where('active', 1)->orderBy('order')->get();
        }

        $data = [
            'municipalities' => Municipality::orderBy('name')->get(),
            'workplaces' => Workplace::orderBy('name')->get(),
            'user' => $user,
            'locales' => Locale::All(),
            'titles' => Title::All(),
            'tracks' => $tracks,
        ];

        return view('pages.settings')->with($data);
    }

    public function storeLanguage(Request $request): RedirectResponse {

        $this->validate($request, [
            'locale' => 'required',
        ]);

        $user = $request->user();
        $user->locale_id = $request->input('locale');
        $user->save();

        return redirect('/settings');
    }

    public function store(Request $request, User $user): RedirectResponse {
        usleep(50000);
        if($user->isNot(Auth::user()) && ! Auth::user()->hasRole('Admin') && (! isset($user->workplace) || ! $user->workplace->workplace_admins->contains('id', Auth::user()->id))) {
            logger("User ".Auth::user()->id." is trying to change settings for user ".$user->id.". Responding with http 403.");
            abort(403);
        }

        $this->validate($request, [
            'workplace' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'mobile' => 'required',
            'title' => 'required',
            'terms_of_employment' => 'required',
            'full_or_part_time' => 'required',
        ],
        [
            'workplace.required' => __('Du måste ange din arbetsplats!'),
            'terms_of_employment.required' => __('Du måste ange anställningsvillkor!'),
            'full_or_part_time.required' => __('Du måste ange anställningens omfattning!'),
            'email.required' => __('Du måste ange din e-postadress!'),
            'email.unique' => __('E-postadressen du har angett finns registrerad på en annan användare!'),
            'mobile.required' => __('Du måste ange ditt mobilnummer!'),
            'title.required' => __('Du måste ange din befattning!'),
            'email.email' => __('vänligen ange en giltig e-postadress!'),
        ]);

        $user->tracks()->sync($request->tracks);
        $user->email = $request->input('email');
        $user->mobile = $request->input('mobile');
        if($request->input('firstname')) {
            $user->firstname = $request->input('firstname');
        } else {
            $user->firstname = $user->saml_firstname;
        }
        $user->name = $user->firstname." ".$user->lastname;
        $user->title_id = $request->input('title');
        $user->terms_of_employment = $request->input('terms_of_employment');
        $user->full_or_part_time = $request->input('full_or_part_time');
        $user->workplace_id = $request->input('workplace');
        $user->use_subtitles = $request->input('use_subtitles');
        $user->save();

        $user->assignRole('Registrerad');

        if($request->session()->has('orig_requested_url')) {
            $orig_requested_url = $request->session()->pull('orig_requested_url');
            return redirect($orig_requested_url)->with('success', __('Inställningarna sparade'));
        } else {
            return redirect('/')->with('success', __('Inställningarna sparade'));
        }
    }
}
