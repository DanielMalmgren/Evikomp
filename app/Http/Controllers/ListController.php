<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\LessonList;
use App\Track;
use App\Lesson;
use App\Workplace;
use App\User;
use Illuminate\Http\RedirectResponse;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        $data = [
            'my_lists' => $user->lesson_lists_owned,
            'shared_lists' => $user->all_lesson_lists(false),
        ];
        
        return view('lists.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all()->sortBy('name');
        } else {
            $workplaces = Auth::user()->admin_workplaces;
        }

        $lesson = null;
        if(isset($request->lesson_id)) {
            $lesson = Lesson::find($request->lesson_id);
        }

        $data = [
            'tracks' => Track::where('active', true)->get(),
            'lessons' => Lesson::where('active', true)->get(),
            'workplaces' => $workplaces,
            'lesson' => $lesson,
        ];
        
        return view('lists.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => __('Du måste ange ett namn på din lista!'),
        ]);

        $list = new LessonList;
        $list->name = $request->name;
        $list->user_id = Auth::user()->id;
        $list->save();

        return $this->update($request, $list);
    }

    /**
     * Display the specified resource.
     *
     * @param  LessonList $list
     * @return \Illuminate\View\View
     */
    public function show(LessonList $list)
    {
        $data = [
            'list' => $list,
            'lessons' => $list->lessons->sortBy('pivot.order'),
            'can_edit' => $list->user->is(Auth::user()) || Auth::user()->hasRole('Admin')
        ];
        return view('lists.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  LessonList $list
     * @return \Illuminate\View\View
     */
    public function edit(LessonList $list)
    {
        if($list->user->isNot(Auth::user()) && ! Auth::user()->hasRole('Admin')) {
            logger("User ".Auth::user()->id." is trying to edit list ".$list->id.". Responding with http 403.");
            abort(403);
        }

        if (Auth::user()->hasRole('Admin')) {
            $workplaces = Workplace::all()->sortBy('name');
        } else {
            $workplaces = Auth::user()->admin_workplaces;
        }

        $data = [
            'list' => $list,
            'tracks' => Track::where('active', true)->get(),
            'lessons' => Lesson::where('active', true)->get(),
            'workplaces' => $workplaces,
        ];
        
        return view('lists.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  LessonList $list
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LessonList $list)
    {
        if($list->user->isNot(Auth::user()) && ! Auth::user()->hasRole('Admin')) {
            logger("User ".Auth::user()->id." is trying to edit list ".$list->id.". Responding with http 403.");
            abort(403);
        }

        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => __('Du måste ange ett namn på din lista!'),
        ]);

        $list->name = $request->name;
        $list->save();

        $list->lessons()->sync($request->lessons);
        $list->workplaces()->sync($request->workplaces);
        $list->users()->sync($request->users);

        return redirect('/lists')->with('success', 'Listan har sparats');
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(LessonList $list)
    {
        $list->delete();
    }

    //Attach or detach a list to a lesson
    public function lessonAttach(Request $request)
    {
        $lesson = Lesson::find($request->lesson);
        $list = LessonList::find($request->list);
        if($request->attach === "true") {
            $lesson->lesson_lists()->attach($list);
        } else {
            $lesson->lesson_lists()->detach($list);
        }        
    }

    //Make a copy of this list
    public function replicate(LessonList $list): RedirectResponse {
        $newList = $list->replicate();
        $newList->name = $newList->name.' ('.__('kopia').')';
        $newList->push();

        foreach($list->lessons as $lesson)
        {
            $newList->lessons()->attach($lesson);
        }

        return redirect('/lists')->with('success', 'Listan har kopierats');
    }
}
