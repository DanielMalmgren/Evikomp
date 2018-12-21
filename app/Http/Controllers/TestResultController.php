<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TestSession;

class TestResultController extends Controller
{
    public function show($testsession_id) {
        $test_session = TestSession::find($testsession_id);
        //$correct_on_first = $test_session->test_responses->where('wrong_responses', 0)->count();
        //$percent = round(100*($test_session->correct_on_first/$test_session->number_of_questions));
        $data = array(
            'test_session' => $test_session
        );

        return view('pages.testresult')->with($data);
    }
}
