<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;

class TagController extends Controller
{
    public function show($tag) {

        $lessons = Lesson::whereRaw("id in (select contents.lesson_id from content_translations, contents where content_translations.content_id=contents.id and text like ? group by contents.lesson_id)", ['%#'.$tag.'%'])->get();

        $data = array(
            'lessons' => $lessons,
            'tag' => $tag,
        );

        return view('tags.show')->with($data);
    }
}
