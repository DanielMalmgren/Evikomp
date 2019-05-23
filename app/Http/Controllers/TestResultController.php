<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\TestSession;

class TestResultController extends Controller
{
    public function show(TestSession $test_session) {

        if($test_session->percent() === 100) {
            $resulttext = __('Grattis, du hade rätt på :percent% av frågorna på första försöket!', ['percent' => $test_session->percent()]);
        } elseif($test_session->percent() > 74) {
            $resulttext = __('Du hade rätt på :percent% av frågorna på första försöket!', ['percent' => $test_session->percent()]);
        } else {
            $resulttext = __('Du hade bara rätt på :percent% av frågorna på första försöket!', ['percent' => $test_session->percent()]);
        }

        $data = [
            'test_session' => $test_session,
            'lesson' => Auth::user()->next_lesson(),
            'resulttext' => $resulttext,
        ];

        return view('pages.testresult')->with($data);
    }
}
