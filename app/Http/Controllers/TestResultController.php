<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\TestSession;
use App\Lesson;
use App\LessonResult;
use PDF;

class TestResultController extends Controller
{
    public function show(Request $request) {
        if(isset($request->test_session_id)) {
            $lesson_has_test = true;
            $test_session = TestSession::find($request->test_session_id);
            $lesson = $test_session->lesson;
            $percent = $test_session->percent();
        } elseif(isset($request->lesson_id)) {
            $user = \Auth::user();
            $lesson = Lesson::find($request->lesson_id);
            if($lesson->questions->isNotEmpty()) {
                abort(403);
            }
            $lesson_result = LessonResult::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $request->lesson_id]
            );
            $lesson->send_notification($user);
            $lesson_has_test = false;
            $percent = 100;
        } else {
            abort(403);
        }

        $data = [
            'lesson' => $lesson,
            'percent' => $percent,
            'lesson_has_test' => $lesson_has_test,
        ];

        return view('pages.testresult')->with($data);
    }

    public function pdfdiploma(Request $request, Lesson $lesson) {

        $user = \Auth::user();

        $track_lessons = $lesson->track->lessons
                         ->whereIn('id', $user->lesson_results->pluck('lesson_id'))
                         ->sortBy('order');

        $data = [
            'lesson' => $lesson,
            'name' => $user->name,
            'track_lessons' => $track_lessons,
        ];

        //return view('lessons.pdfdiploma')->with($data);

        $pdf = PDF::loadView('lessons.pdfdiploma', $data);

        return $pdf->download('Evikomp_diplom.pdf');
    }

}
