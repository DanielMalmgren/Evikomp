<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Announcement;
use App\ClosedMonth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index() {
        if(empty(Auth::user()["workplace_id"]) || ! Auth::user()->accepted_gdpr) {
            return redirect('/firstlogin');
        } else {
            $announcements = Announcement::All()->sort()->reverse()->take(5);

            setlocale(LC_TIME, Auth::user()->locale_id);
            $previous_month = date("m", strtotime("first day of previous month"));
            $previous_month_year = date("Y", strtotime("first day of previous month"));
            $monthstr = strftime('%B', strtotime("first day of previous month"));

            $last_month_is_closed = ClosedMonth::where('month', $previous_month)->where('year', $previous_month_year)->exists();
            $last_month_is_attested = Auth::user()->time_attests->where('attestlevel', 1)->where('month', $previous_month)->where('year', $previous_month_year)->isNotEmpty();
            $time_rows = Auth::user()->time_rows($previous_month_year, $previous_month);
            $time = end($time_rows)[32];

            if(isset(Auth::user()->workplace->polls)) {
                $poll = Auth::user()->workplace->polls
                    ->whereIn('scope_terms_of_employment', [Auth::user()->terms_of_employment, 0])
                    ->whereIn('scope_full_or_part_time', [Auth::user()->full_or_part_time, 0])
                    ->first();
            } else {
                $poll = null;
            }

            $data = [
                'announcements' => $announcements,
                'lesson' => Auth::user()->next_lesson(),
                'should_attest' => !$last_month_is_closed && !$last_month_is_attested && $time>=1.0 && Auth::user()->workplace->includetimeinreports,
                'previous_month' => $previous_month,
                'monthstr' => $monthstr,
                'poll' => $poll,
            ];
            return view('pages.index')->with($data);
        }
    }

    public function about(): View {
        return view('pages.about');
    }

    public function unsecurelogin(): View {
        return view('pages.unsecurelogin');
    }

    public function logout(): View {
        session()->flush();
        Auth::logout();
        return view('pages.logout');
    }
}
