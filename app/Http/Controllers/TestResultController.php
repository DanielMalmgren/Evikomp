<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TestSession;

class TestResultController extends Controller
{
    public function show(TestSession $test_session) {
        $data = array(
            'test_session' => $test_session
        );

        return view('pages.testresult')->with($data);
    }
}
