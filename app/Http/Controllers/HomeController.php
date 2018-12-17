<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Track;
use App\Municipality;
use App\Workplace;
use App\User;
use App\Lesson;
use App\Locale;
use App\Question;
use App\ResponseOption;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    public function index() {
        if(empty(Auth::user()["workplace_id"])) {
            return redirect('/firstlogin');
        } else {
            return view('pages.index');
        }
    }

    public function about() {
        return view('pages.about');
    }
}
