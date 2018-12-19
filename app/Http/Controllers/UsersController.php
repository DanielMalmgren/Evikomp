<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    public function show($user_id = null) {
        if($user_id) {
            $data = array(
                'user' => User::find($user_id)
            );
        } else {
            $data = array(
                'user' => Auth::user()
            );
        }

        return view('pages.userinfo')->with($data);
    }

    public function index() {
        $users = User::all();
        $data = array(
            'users' => $users
        );
        return view('pages.listusers')->with($data);
    }

    public function export() {
        return Excel::download(new UsersExport, 'Deltagare_Evikomp.xlsx');
    }
}
