<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\LessonList;
use App\Track;
use App\lesson;
use App\Workplace;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $data = [
            'my_lists' => $user->lesson_lists_owned,
            'shared_lists' => $user->lesson_lists, //TODO: Behöver även inkludera dem som delats med min arbetsplats!
        ];
        
        return view('lists.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'tracks' => Track::where('active', true)->get(),
            'lessons' => Lesson::where('active', true)->get(),
        ];
        
        return view('lists.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(LessonList $list)
    {
        $data = [
            'list' => $list,
            'lessons' => $list->lessons,
        ];
        return view('lists.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  LessonList $list
     * @return \Illuminate\Http\Response
     */
    public function edit(LessonList $list)
    {
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LessonList $list)
    {
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

        return redirect('/lists')->with('success', 'Listan har sparats');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
}
