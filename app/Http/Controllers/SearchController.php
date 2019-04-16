<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;

class SearchController extends Controller
{
    //Return a json containing lessons matching a search string sent from a select2 object. See https://select2.org/data-sources/ajax
    public function select2(Request $request) {
        $lessons = Lesson::whereTranslationLike('name', '%'.$request->q.'%')->orWhereHas('contents', function ($q) use($request){
            $q->where('content', 'like', '%'.$request->q.'%')->orWhereTranslationLike('text', '%'.$request->q.'%');
        })->get();

        $results = ['results' => []];

        foreach($lessons as $key => $lesson) {
            $results['results'][$key] = [
                'id' => $lesson->id,
                'text' => $lesson->name
            ];
        }

        return $results;
    }
}
