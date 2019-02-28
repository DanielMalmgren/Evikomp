<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Announcement;

class HomeController extends Controller
{
    public function index() {
        if(empty(Auth::user()["workplace_id"]) || !Auth::user()->accepted_gdpr) {
            return redirect('/firstlogin');
        } else {
            $announcements = Announcement::All()->sort()->reverse()->take(5);

            $data = array(
                'announcements' => $announcements,
                'lesson' => Auth::user()->next_lesson()
            );
            return view('pages.index')->with($data);
        }
    }

    public function about() {
        return view('pages.about');
    }

    public function logout() {
        logger('Loggar ut');
        Auth::logout();
        return view('pages.logout');
    }
}
