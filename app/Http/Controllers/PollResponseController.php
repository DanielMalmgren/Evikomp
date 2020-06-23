<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Poll;
use App\PollResponse;
use App\PollQuestion;

class PollResponseController extends Controller
{
    public function store(Request $request) {
        /*usleep(50000);
        $this->validate($request, [
            'text' => 'required',
            'correctAnswers' => 'required|integer',
            'lesson_id' => 'required',
        ]);

        $lesson = Lesson::find($request->lesson_id);

        $question = new Question();
        $question->lesson_id = $lesson->id;
        $question->order = $lesson->questions->max('order')+1;
        $question->save();

        return $this->update($request, $question);*/

        $question = PollQuestion::find($request->poll_question_id);
        //$poll = $question->poll;
        $nextquestion = $question->next_question();

        if(isset($nextquestion)) {
            return redirect('/pollquestion/'.$nextquestion->id);
        } else {
            return view('polls.feedback');
        }
    }
}
