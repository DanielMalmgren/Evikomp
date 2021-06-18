<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\TestSession;
use App\Lesson;
use App\LessonResult;
use PDF;

class LessonResultController extends Controller
{
    public function show(Request $request, Lesson $lesson) {

        $user = \Auth::user();

        $lesson_result = LessonResult::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id]
        );
        $request->session()->put('lesson_result_id', $lesson_result->id);

        $poll = $lesson->poll;
        $poll_compulsory = $lesson->poll_compulsory;
        if($poll !== null && $user->poll_sessions->where('poll_id', $lesson->poll->id)->isNotEmpty()) {
            $poll = null;
            $poll_compulsory = false;
        }

        $lesson_has_test = false;
        $passed_now = false;
        $failed = false;
        $passed_earlier = false;
        $personal_best = 0;
        $percent = 0;

        if($lesson->questions->isNotEmpty()) { //There is a test
            $test_session = TestSession::find($request->session()->get('test_session_id'));
            $personal_best = $lesson_result->personal_best_percent;
            $lesson_has_test = true;

            if($test_session) { //The user just came back from doing a test
                $percent = max($test_session->percent(), $personal_best);
                if($test_session->percent() >= $lesson->test_required_percent) {
                    //The user cleared the test!
                    $passed_now = true;
                } else {
                    //The user failed the test
                    $failed = true;
                }
            } else {
                if($personal_best < $lesson->test_required_percent) {
                    //The user has never finished the test, do it now!
                    return redirect('/test/'.$lesson->id);
                } else {
                    //The user has finished the test earlier
                    $passed_earlier = true;
                    $percent = $personal_best;
                }
            }
        } else {
            $percent = 100;
            $passed_now = true;
        }

        if(!$poll_compulsory) {
            $request->session()->forget('test_session_id');
            $request->session()->forget('lesson_result_id');
            if($passed_now) {
                $lesson->times_finished++;
                $lesson->save();
                $lesson->send_notification($user);
                if($percent > $lesson_result->personal_best_percent) {
                    $lesson_result->personal_best_percent = $percent;
                    $lesson_result->save();
                }
            }
        }

        $data = [
            'lesson' => $lesson,
            'percent' => $percent,
            'lesson_has_test' => $lesson_has_test,
            'poll' => $poll,
            'poll_compulsory' => $poll_compulsory,
            'passed_now' => $passed_now,
            'failed' => $failed,
            'passed_earlier' => $passed_earlier,
        ];

        return view('lessons.result')->with($data);
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
            'user_id' => $user->id,
        ];

        //return view('lessons.pdfdiploma')->with($data);

        $pdf = PDF::loadView('lessons.pdfdiploma', $data);

        return $pdf->download('Evikomp_diplom.pdf');
    }

}
