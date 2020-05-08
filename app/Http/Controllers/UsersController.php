<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Exports\UsersExport;

class UsersController extends Controller
{
    public function show(User $user) {
        if($user != Auth::user() && ! Auth::user()->hasRole('Admin') && (! isset($user->workplace) || ! $user->workplace->workplace_admins->contains('id', Auth::user()->id))) {
            abort(403);
        }

        if($user->workplace){
            $tracks = $user->tracks->merge($user->workplace->tracks)->sort();
        } else {
            $tracks = collect([]);
        }

        $data = array(
            'user' => $user,
            'totalactivehours' => round($user->active_times->sum('seconds')/3600),
            'totalprojecthours' => round($user->project_times->sum('minutes_total')/60),
            'attestedhourslevel1' => round($user->time_attests->where('attestlevel', 1)->sum('hours')),
            'attestedhourslevel3' => round($user->time_attests->where('attestlevel', 3)->sum('hours')),
            'tracks' => $tracks,
        );

        return view('users.show')->with($data);
    }

    public function index() {
        //$users = User::all();
        $workplaces = Auth::user()->admin_workplaces;
        $users = User::all()->whereIn('workplace_id', $workplaces->pluck('id'));
        $data = [
            'users' => $users,
            'workplaces' => $workplaces,
        ];
        return view('users.index')->with($data);
    }

    /*public function export() {
        return Excel::download(new UsersExport(), 'Deltagare_Evikomp.xlsx');
    }*/

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

    private static function randomPassword() {
        $alphabet = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 10; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function create() {
        $password = $this->randomPassword();
        $data = [
            'password' => $password,
        ];
        return view('users.create')->with($data);
    }

    public function store(Request $request) {
        usleep(50000);

        $this->validate($request, [
            'firstname' => 'required|string|between:2,255',
            'lastname' => 'required|string|between:2,255',
            'personid' => 'required|numeric|between:190001010000,203012319999|unique:users',
            'email' => 'required|string|email|unique:users',
            'pwd_cleartext' => 'required|string|between:8,255',
        ],
        [
            'firstname.required' => __('Du måste ange ett förnamn!'),
            'firstname.string' => __('Du måste ange ett förnamn!'),
            'firstname.between' => __('Du måste ange ett förnamn!'),
            'lastname.required' => __('Du måste ange ett efternamn!'),
            'lastname.string' => __('Du måste ange ett efternamn!'),
            'lastname.between' => __('Du måste ange ett efternamn!'),
            'personid.required' => __('Du måste ange ett personnummer!'),
            'personid.numeric' => __('Du måste ange ett giltigt personnummer i rätt format!'),
            'personid.between' => __('Du måste ange ett giltigt personnummer i rätt format!'),
            'personid.unique' => __('En användare med detta personnummer finns redan registrerad!'),
            'email.required' => __('Du måste ange en e-postadress!'),
            'email.string' => __('Du måste ange en e-postadress!'),
            'email.email' => __('Du måste ange en e-postadress!'),
            'email.unique' => __('En användare med denna e-postadress finns redan registrerad!'),
            'pwd_cleartext.required' => __('Du måste ange ett lösenord!'),
            'pwd_cleartext.string' => __('Du måste ange ett lösenord!'),
            'pwd_cleartext.between' => __('Lösenordet du anger måste vara minst 8 tecken!'),
        ]);

        $user = new User();
        $user->firstname = $request->firstname;
        $user->saml_firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->name = $request->firstname.' '.$request->lastname;
        $user->personid = $request->personid;
        $user->email = $request->email;
        $user->password = Hash::make($request->pwd_cleartext);
        $user->save();

        return redirect('/')->with('success', __('Användaren har skapats'));
    }
}
