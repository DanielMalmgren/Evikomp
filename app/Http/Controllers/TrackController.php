<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Track;

class TrackController extends Controller
{
    public function index() {
        $tracks = Track::all();
        $data = array(
            'tracks' => $tracks
        );
        return view('pages.tracks')->with($data);
    }

    public function show($track_id) {
        $track = Track::where('id', $track_id)->first();
        $lessons = $track->lessons()->get();
        $data = array(
            'track' => $track,
            'lessons' => $lessons
        );
        return view('pages.track')->with($data);
    }
}
