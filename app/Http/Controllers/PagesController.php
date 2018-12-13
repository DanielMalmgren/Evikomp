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
use App\Question;
use App\ResponseOption;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class PagesController extends Controller
{
    public function __construct() {
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
                'municipalities' => Municipality::orderBy('name')->get(),
                'workplaces' => Workplace::orderBy('name')->get(),
                'locales' => Locale::All(),
                'user' => Auth::user()
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
        $question = Question::where('lesson_id', $lesson_id)->first();
        $data = array(
            'question' => $question,
            'lesson' => $lesson
        );
        return view('pages.lesson')->with($data);
    }

    public function test($lesson_id, $order = null) {
        $lesson = Lesson::where('id', $lesson_id)->first();
        //Om $order är null, börja med första frågan
        if(!$order) {
            $question = Question::where('lesson_id', $lesson_id)->first();
        } else {
            $question = Question::where([['lesson_id', '=', $lesson_id],['order', '>=', $order]])->first();
            if(!$question) {
                //Om det inte finns någon fråga med $order är testet klart, dirigera om till sidan med resultat
                return redirect('/testresult/'.$lesson_id);
            }
        }

        $responseoptions = ResponseOption::where('question_id', $question->id)->get();

        $data = array(
            'question' => $question,
            'lesson' => $lesson,
            'responseoptions' => $responseoptions
        );
        return view('pages.question')->with($data);
    }

    public function testresult($lesson_id) {
        return view('pages.testresult');
    }

    public function storeFirstLoginLanguage(Request $request) {

        $this->validate($request, [
            'locale' => 'required'
        ]);

        $user = $request->user();
        $user->locale_id = $request->input('locale');
        $user->save();

        return redirect('/');
    }

    public function storeFirstLogin(Request $request) {

        //logger(print_r($request->all(), true));

        $this->validate($request, [
            'workplace' => 'required',
            'email' => 'required'
        ]);

        $user = $request->user();
        $user->email = $request->input('email');
        $user->workplace_id = $request->input('workplace');
        $user->save();

        $user->assignRole('Registered');

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

    public function exportUsers() {
        return Excel::download(new UsersExport, 'Deltagare_Evikomp.xlsx');
    }

}
