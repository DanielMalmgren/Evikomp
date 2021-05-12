<?php

namespace App\Http\Controllers;

use App\Announcement;
use Illuminate\Http\Request;

class AnnouncementsController extends Controller
{
    public function __construct() {
        $this->middleware('permission:manage announcements', ['except' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = Announcement::All()->sort()->reverse();

        $data = [
            'announcements' => $announcements,
        ];
        return view('announcements.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'heading' => 'required',
            'bodytext' => 'required',
        ]);

        $announcement = new Announcement();
        $announcement->heading = $request->heading;
        $announcement->bodytext = $request->bodytext;
        $announcement->preamble = $request->preamble;
        $announcement->save();

        return redirect('/announcements/'.$announcement->id)->with('success', 'Meddelandet sparat');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        $data = [
            'announcement' => $announcement,
        ];
        return view('announcements.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        $data = [
            'announcement' => $announcement,
        ];
        return view('announcements.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        $this->validate($request, [
            'heading' => 'required',
            'bodytext' => 'required',
        ]);

        $announcement->heading = $request->heading;
        $announcement->bodytext = $request->bodytext;
        $announcement->preamble = $request->preamble;
        $announcement->save();

        return redirect('/announcements/'.$announcement->id)->with('success', 'Meddelandet sparat');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $announcement)
    {
        usleep(50000);

        $announcement->delete();

        return redirect('/')->with('success', 'Meddelandet raderat');
    }
}
