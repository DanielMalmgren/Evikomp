<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestResultController extends Controller
{
    public function show($lesson_id) {
        return view('pages.testresult');
    }
}
