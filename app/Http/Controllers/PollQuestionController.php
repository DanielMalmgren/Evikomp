<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\PollQuestion;
use App\PollSession;
use App\Poll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class PollQuestionController extends Controller
{
    public function show(PollQuestion $question) {

        $previous = $question->first_on_previous_page();
        if(isset($previous)) {
            $previous_id = $previous->id;
        } else {
            $previous_id = null;
        }

        $poll_session = PollSession::find(session("poll_session_id"));

        if(!isset($poll_session)) {
            return redirect('/')->with('error', __('Kan inte visa enkätfrågan!'));
        }

        $data = [
            'question' => $question,
            'previous_id' => $previous_id,
            'previous_responses' => $poll_session->poll_responses,
        ];

        return view('pollquestions.show')->with($data);
    }

    public function create(Poll $poll) {
        $data = [
            'other_questions' => $poll->poll_questions->where('type', 'select'),
            'poll' => $poll,
        ];
        return view('pollquestions.create')->with($data);
    }

    public function edit(PollQuestion $question) {
        $data = [
            'question' => $question,
            'other_questions' => $question->poll->poll_questions->where('type', 'select')->sortBy('order'),
            'display_criteria_array' => explode('==', $question->display_criteria),
        ];
        return view('pollquestions.edit')->with($data);
    }

    public function store(Request $request): RedirectResponse {
        usleep(50000);
        $this->validate($request, [
            'text' => 'required',
            'type' => 'required',
            'poll_id' => 'required',
        ],
        [
            'text.required' => __('Du måste ange själva frågetexten!'),
            'type.required' => __('Du måste ange vad det är för typ av fråga!'),
        ]);

        $currentLocale = \App::getLocale();
        $poll = Poll::find($request->poll_id);

        $question = new PollQuestion();
        $question->poll_id = $request->poll_id;
        $question->type = $request->type;
        $question->order = $poll->poll_questions->max('order')+1;
        $question->save();

        return $this->update($request, $question);
    }

    public function update(Request $request, PollQuestion $question): RedirectResponse {
        usleep(50000);
        $this->validate($request, [
            'text' => 'required',
            'type' => 'required',
        ],
        [
            'text.required' => __('Du måste ange själva frågetexten!'),
            'type.required' => __('Du måste ange vad det är för typ av fråga!'),
        ]);

        $currentLocale = \App::getLocale();
        $user = Auth::user();
        logger("Poll question ".$question->id." is being edited by ".$user->name);

        $question->alternatives_array = $request->alternative;

        $question->translateOrNew($currentLocale)->text = $request->text;
        $question->type = $request->type;
        $question->min_alternatives = $request->min_alternatives;
        $question->max_alternatives = $request->max_alternatives;
        $question->compulsory = $request->compulsory;
        if($request->display_criteria[0] == -1) {
            $question->display_criteria = '';
        } else {
            $question->display_criteria = implode('==', $request->display_criteria);
        }
        $question->save();

        return redirect('/poll/'.$question->poll->id.'/edit')->with('success', __('Ändringar sparade'));
    }

    public function reorder(Request $request): void {
        parse_str($request->data, $data);
        $ids = $data['id'];

        foreach($ids as $order => $id){
            $lesson = PollQuestion::findOrFail($id);
            $lesson->order = $order+1;
            $lesson->save();
        }
    }

    public function destroy(PollQuestion $question) {
        logger('Destroying poll question '.$question->id);
        $question->delete();
    }

}
