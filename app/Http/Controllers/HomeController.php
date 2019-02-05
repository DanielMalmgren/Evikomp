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

            $active_time = gmdate("H:i:s", Auth::user()->active_times->sum('seconds'));

            $data = array(
                'announcements' => $announcements,
                'active_time' => $active_time
            );
            return view('pages.index')->with($data);
        }
    }

    public function about() {
        return view('pages.about');
    }
}
