<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ActiveTime;
use App\User;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    public function show(User $user = null) {
        if(!$user) {
            $user = Auth::user();
        }

        $active_times = array();
        for($i = 1; $i <= date("j"); $i++) {
            $this_time = $active_times_db = ActiveTime::where('user_id', $user->id)->whereMonth('date', date("n"))->whereDay('date', $i)->first();
            if($this_time) {
                $active_times[$i] = date("H:i:s", $this_time->seconds);
            } else {
                $active_times[$i] = "00:00:00";
            }
        }
        $total_active_time = date("H:i", $user->active_times->sum('seconds')+59);

        $data = array(
            'user' => $user,
            'active_times' => $active_times,
            'total_active_time' => $total_active_time
        );

        return view('pages.userinfo')->with($data);
    }

    public function index() {
        //$users = User::all();
        $workplaces = Auth::user()->admin_workplaces;
        $users = User::all()->whereIn('workplace_id', $workplaces->pluck('id'));
        $data = array(
            'users' => $users,
            'workplaces' => $workplaces
        );
        return view('pages.listusers')->with($data);
    }

    public function export() {
        return Excel::download(new UsersExport, 'Deltagare_Evikomp.xlsx');
    }
}
