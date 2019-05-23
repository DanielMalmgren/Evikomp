<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Track;

class TrackController extends Controller
{
    public function index(Request $request) {
        if($request->showall) {
            $tracks = Track::all();
        } else {
            $tracks = Auth::user()->tracks->merge(Auth::user()->workplace->tracks)->sort();
        }

        $data = [
            'tracks' => $tracks,
            'showall' => $request->showall,
        ];
        return view('tracks.index')->with($data);
    }

    public function show(Track $track) {
        if(Auth::user()->can('list all lessons')) {
            $lessons = $track->lessons->sortBy('order');
        } else {
            $title = Auth::user()->title;

            $lessons = $track->lessons()->where('active', true)
            //->whereHas('tracks', function ($query) use ($track) {
            //    $query->where('id', $track->id);
            //})
                ->where(static function ($query) use ($title) {
                    $query->whereHas('titles', static function ($query) use ($title) {
                        $query->where('id', $title->id);
                    })
                ->orWhere('limited_by_title', false);
                })
                ->orderBy('order')->get();

        }
        $data = [
            'track' => $track,
            'lessons' => $lessons,
        ];
        return view('tracks.show')->with($data);
    }
}
