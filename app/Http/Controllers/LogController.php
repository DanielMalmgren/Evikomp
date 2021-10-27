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
        $lesson = $request->lesson;

        $logrows = Activity::when($user, function ($query, $user) {
                    return $query->where('causer_id', $user);
                })
                ->when($description, function ($query, $description) {
                    return $query->where('description', $description);
                })
                ->when($lesson, function ($query, $lesson) {
                    return $query->where('subject_id', $lesson)->where('subject_type', 'App\Lesson');
                })
                ->get();

        $data = [
            'logrows' => $logrows,
        ];
        return view('log.index')->with($data);
    }
}
