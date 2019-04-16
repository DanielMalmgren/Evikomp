<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;

class SearchController extends Controller
{
    //Return a json containing lessons matching a search string sent from a select2 object. See https://select2.org/data-sources/ajax
    public function select2(Request $request) {
        $lessons = Lesson::whereTranslationLike('name', '%'.$request->q.'%')->get();
//Kolla https://stackoverflow.com/questions/31332360/query-where-column-is-in-another-table
//för att luska ut hur man filtrerar lektioner utefter innehåll i Content
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
