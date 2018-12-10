<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Track;
use App\Municipality;
use App\Workplace;
use App\User;
use App\Lesson;
use App\Locale;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        if (Auth::check()) {
            $title = 'Inloggad användare: '.Auth::user()["name"];
        } else {
            $title = 'Inte inloggad!';
        }

        if(empty(Auth::user()["workplace_id"])) {
            $data = array(
                'municipalities' => Municipality::all(),
                'workplaces' => Workplace::all(),
                'locales' => Locale::All()
            );
            return view('pages.firstlogin')->with($data);
        } else {
            return view('pages.index')->with('title', $title);
        }
    }

    public function about() {
        return view('pages.about');
    }

    public function userinfo($user_id = null) {
        if($user_id) {
            $data = array(
                'user' => User::find($user_id)
            );
        } else {
            $data = array(
                'user' => Auth::user()
            );
        }

        return view('pages.userinfo')->with($data);
    }

    public function settings() {
        $data = array(
            'user' => Auth::user(),
            'locales' => Locale::All()
        );

        return view('pages.settings')->with($data);
    }

    public function tracks() {
        $tracks = Track::all();
        $data = array(
            'tracks' => $tracks
        );
        return view('pages.tracks')->with($data);
    }

    public function track($track_id) {
        $track = Track::where('id', $track_id)->first();
        $lessons = $track->lessons()->get();
        $data = array(
            'track' => $track,
            'lessons' => $lessons
        );
        return view('pages.track')->with($data);
    }

    public function lesson($lesson_id) {
        $lesson = Lesson::where('id', $lesson_id)->first();
        $data = array(
            'lesson' => $lesson
        );
        return view('pages.lesson')->with($data);
    }

    public function storeFirstLogin(Request $request) {

        //logger(print_r($request->all(), true));

        $this->validate($request, [
            'workplace' => 'required',
            'email' => 'required',
            'locale' => 'required'
        ]);

        $user = $request->user();
        $user->email = $request->input('email');
        $user->workplace_id = $request->input('workplace');
        $user->locale_id = $request->input('locale');
        $user->save();

        return redirect('/')->with('success', 'Uppgifterna sparade');
    }

    public function storeSettings(Request $request) {

        //logger(print_r($request->all(), true));

        $this->validate($request, [
            'locale' => 'required'
        ]);

        $user = $request->user();
        $user->locale_id = $request->input('locale');
        $user->save();

        return redirect('/')->with('success', 'Uppgifterna sparade');
    }

    public function listUsers() {
        $users = User::all();
        $data = array(
            'users' => $users
        );
        return view('pages.listusers')->with($data);
    }

}
