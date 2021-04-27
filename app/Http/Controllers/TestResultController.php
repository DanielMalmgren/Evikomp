<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\TestSession;
use PDF;

class TestResultController extends Controller
{
    public function show(TestSession $test_session) {

        $data = [
            'nextlesson' => Auth::user()->next_lesson(),
            'lesson' => $test_session->lesson,
            'percent' => $test_session->percent(),
            'test_session_id' => $test_session->id,
        ];

        return view('pages.testresult')->with($data);
    }

    public function pdfdiploma(TestSession $test_session, Request $request) {

        $user = \Auth::user();

        $track_lessons = $test_session->lesson->track->lessons
                         ->whereIn('id', $user->lesson_results->pluck('lesson_id'));

        $data = [
            'lesson' => $test_session->lesson,
            'name' => $user->name,
            'track_lessons' => $track_lessons,
        ];

        $pdf = PDF::loadView('lessons.pdfdiploma', $data);

        return $pdf->download('Evikomp_diplom.pdf');
    }

}
