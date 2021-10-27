<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        if(!$user->can('read logs')) {
            abort(403);
        }

        $user = $request->user;
        $description = $request->description;
        $subject_id = $request->subject_id;
        $subject_type = $request->subject_type;

        $logrows = Activity::when($user, function ($query, $user) {
                    return $query->where('causer_id', $user);
                })
                ->when($description, function ($query, $description) {
                    return $query->where('description', $description);
                })
                ->when($subject_id, function ($query, $subject_id) use ($subject_type) {
                    return $query->where('subject_id', $subject_id)->where('subject_type', $subject_type);
                })
                ->get();

        $data = [
            'logrows' => $logrows,
        ];
        return view('log.index')->with($data);
    }
}
