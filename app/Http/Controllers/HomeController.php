<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Announcement;
use App\ClosedMonth;

class HomeController extends Controller
{
    public function index() {
        if(empty(Auth::user()["workplace_id"]) || ! Auth::user()->accepted_gdpr) {
            return redirect('/firstlogin');
        } else {
            $announcements = Announcement::All()->sort()->reverse()->take(5);

            $last_month_is_closed = ClosedMonth::all()->where('month', date("m", strtotime("first day of previous month")))->where('year', date("Y", strtotime("first day of previous month")))->isNotEmpty();
            /*if($last_month_is_closed) {
                logger('Föregående månad är stängd');
            } else {
                logger('Föregående månad är fortfarande öppen');
            }*/

            $data = [
                'announcements' => $announcements,
                'lesson' => Auth::user()->next_lesson(),
            ];
            return view('pages.index')->with($data);
        }
    }

    public function about() {
        return view('pages.about');
    }

    public function unsecurelogin() {
        return view('pages.unsecurelogin');
    }

    public function logout() {
        session()->flush();
        Auth::logout();
        return view('pages.logout');
    }
}
