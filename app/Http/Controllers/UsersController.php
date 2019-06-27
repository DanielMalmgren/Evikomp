<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    /*public function show(User $user = null) {
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
    }*/

    public function index() {
        //$users = User::all();
        $workplaces = Auth::user()->admin_workplaces;
        $users = User::all()->whereIn('workplace_id', $workplaces->pluck('id'));
        $data = [
            'users' => $users,
            'workplaces' => $workplaces,
        ];
        return view('pages.listusers')->with($data);
    }

    public function export() {
        return Excel::download(new UsersExport(), 'Deltagare_Evikomp.xlsx');
    }

    //Return a json containing users matching a search string sent from a select2 object. See https://select2.org/data-sources/ajax
    public function select2(Request $request) {
        $users = User::where('name', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%')->orWhere('personid', 'like', '%'.$request->q.'%')->get();

        $results = ['results' => []];

        foreach($users as $key => $user) {
            $results['results'][$key] = [
                'id' => $user->id,
                'text' => $user->name.' ('.$user->email.')',
            ];
        }

        return $results;
    }

    //The following function will not really delete a user, just remove it from the workplace
    public function destroy(User $user) {
        $user->workplace_id = null;
        $user->save();
    }
}
