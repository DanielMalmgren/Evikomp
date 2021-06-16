<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Announcement;
use App\ClosedMonth;
use App\PollQuestion;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index() {
        $announcements = Announcement::All()->sort()->reverse()->take(5);
        $user = Auth::user();

        setlocale(LC_TIME, $user->locale_id);
        $previous_month = date("m", strtotime("first day of previous month"));
        $previous_month_year = date("Y", strtotime("first day of previous month"));
        $monthstr = strftime('%B', strtotime("first day of previous month"));

        $last_month_is_attested = $user->month_is_fully_attested($previous_month_year, $previous_month, 0.5);

        $time=Auth::user()->month_total_time($previous_month_year, $previous_month);

        if(isset(Auth::user()->workplace->polls)) {
            $now = new \Carbon\Carbon();
            $poll = $user->workplace->polls
                ->whereNotIn('id', $user->poll_sessions->where('finished', true)->pluck('poll_id'))
                ->whereIn('scope_terms_of_employment', [$user->terms_of_employment, 0])
                ->whereIn('scope_full_or_part_time', [$user->full_or_part_time, 0])
                ->where('active_from', '<=', $now)
                ->where('active_to', '>=', $now)
                ->whereIn('id', PollQuestion::all()->pluck('poll_id'))
                ->first();
        } else {
            $poll = null;
        }

        $data = [
            'announcements' => $announcements,
            //'lesson' => Auth::user()->next_lesson(),
            'should_attest' => !$last_month_is_attested && $time>=1.0 && Auth::user()->workplace->includetimeinreports,
            //'previous_month' => $previous_month,
            'monthstr' => $monthstr,
            'poll' => $poll,
            'shared_lists' => $user->all_lesson_lists(true),
        ];
        return view('pages.index')->with($data);
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
