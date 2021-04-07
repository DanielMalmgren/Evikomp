<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lesson;
use App\Track;
use App\Announcement;
use App\ProjectTime;

class SearchController extends Controller
{
    //Return a json containing lessons, tracks and announcements matching a search string sent from a select2 object. See https://select2.org/data-sources/ajax
    public function select2(Request $request) {
        $results = ['results' => []];

        $i=-1;

        if (Auth::user()->hasRole('Admin')) {
            $mindate = date("Y-m-d", strtotime("first day of previous month"));
            $project_time = ProjectTime::find($request->q);
            if($project_time !== null && $project_time->date >= $mindate) {
                $i++;
                $results['results'][$i]['text'] = __('Attestering från lista');
                $results['results'][$i]['children'][0] = [
                    'id' => $project_time->id,
                    'text' => $project_time->id,
                    'url' => '/projecttime/attest_from_list/'.$project_time->id,
                ];
            }    
        }

        $lessons = Lesson::whereTranslationLike('name', '%'.$request->q.'%')->orWhereHas('contents', static function ($query) use($request){
            $query->where('content', 'like', '%'.$request->q.'%')->orWhereTranslationLike('text', '%'.$request->q.'%');
        })->get();
        if($lessons->isNotEmpty()) {
            $i++;
            $results['results'][$i]['text'] = __('Moduler');
            foreach($lessons as $key => $lesson) {
                $results['results'][$i]['children'][$key] = [
                    'id' => $lesson->id,
                    'text' => $lesson->name,
                    'url' => '/lessons/'.$lesson->id,
                ];
            }
        }

        $tracks = Track::whereTranslationLike('name', '%'.$request->q.'%')->orWhereTranslationLike('subtitle', '%'.$request->q.'%')->get();
        if($tracks->isNotEmpty()) {
            $i++;
            $results['results'][$i]['text'] = __('Spår');
            foreach($tracks as $key => $track) {
                $results['results'][$i]['children'][$key] = [
                    'id' => $track->id,
                    'text' => $track->name,
                    'url' => '/tracks/'.$track->id,
                ];
            }
        }

        $announcements = Announcement::where('heading', 'like', '%'.$request->q.'%')->orWhere('preamble', 'like', '%'.$request->q.'%')->get();
        if($announcements->isNotEmpty()) {
            $i++;
            $results['results'][$i]['text'] = __('Nyheter');
            foreach($announcements as $key => $announcement) {
                $results['results'][$i]['children'][$key] = [
                    'id' => $announcement->id,
                    'text' => $announcement->heading,
                    'url' => '/announcements/'.$announcement->id,
                ];
            }
        }

        return $results;
    }
}
