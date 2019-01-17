<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Track;
use App\User;

class TrackController extends Controller
{
    public function index() {
        $tracks = Auth::user()->tracks->merge(Auth::user()->workplace->tracks)->sort();

        $data = array(
            'tracks' => $tracks
        );
        return view('pages.tracks')->with($data);
    }

    public function show(Track $track) {
        //$track = Track::where('id', $track_id)->first();
        //$track = Track::find($track_id);
        $data = array(
            'track' => $track
        );
        return view('pages.track')->with($data);
    }
}
