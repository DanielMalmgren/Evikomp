<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Track;
use App\User;
use PDF;

class TrackController extends Controller
{
    public function index(Request $request) {
        if($request->showall) {
            if(Auth::user()->can('list all lessons')) {
                $tracks = Track::orderBy('order')->get();
            } else {
                $tracks = Track::where('active', 1)->orderBy('order')->get();
            }
        } else if(isset(Auth::user()->workplace)){
            $tracks = Auth::user()->tracks->merge(Auth::user()->workplace->tracks)->sortBy('order');
        } else {
            $tracks = collect([]);
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
            'is_editor' => Auth::user()->admin_tracks->where('id', $track->id)->isNotEmpty(),
        ];
        return view('tracks.show')->with($data);
    }

    public function reorder(Request $request) {
        parse_str($request->data, $data);
        $ids = $data['id'];

        foreach($ids as $order => $id){
            $track = Track::findOrFail($id);
            $track->order = $order+1;
            $track->save();
        }
    }

    public function create() {
        return view('tracks.create');
    }

    public function store(Request $request) {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => __('Du måste ange ett namn på spåret!'),
        ]);

        $track = new Track();
        $track->id = Track::max('id')+1;
        $track->save();

        return $this->update($request, $track);
    }

    public function edit(Track $track) {
        $data = [
            'track' => $track,
        ];
        return view('tracks.edit')->with($data);
    }

    public function update(Request $request, Track $track) {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
            'id' => 'integer|min:0',
        ],
        [
            'name.required' => __('Du måste ange ett namn på spåret!'),
            'id.integer' => __('Du måste ange ett positivt nummer för spåret!'),
            'id.min' => __('Du måste ange ett positivt nummer för spåret!'),
        ]);

        $currentLocale = \App::getLocale();
        $user = Auth::user();
        logger("Track ".$track->id." is being edited by ".$user->name);

        if($request->new_admins) {
            foreach($request->new_admins as $user_id) {
                $user = User::find($user_id);
                if(isset($user)) {
                    logger('Making '.$user->name.' editor for track '.$track->id);

                    $user->admin_tracks()->attach($track);
                    $user->assignRole('Track editor');
                }
            }
        }

        if($request->remove_admin) {
            foreach(array_keys($request->remove_admin) as $user_id) {
                $user = User::find($user_id);
                logger('Removing '.$user->name.' as editor for track '.$track->id);
                $user->admin_tracks()->detach($track);
                if($user->admin_tracks->isNotEmpty()) {
                    $user->removeRole('Track editor');
                }
            }
        }

        $track->translateOrNew($currentLocale)->name = $request->name;
        $track->translateOrNew($currentLocale)->subtitle = $request->subtitle;
        $track->active = $request->active;
        $track->save();

        return redirect('/tracks/'.$track->id)->with('success', __('Ändringar sparade'));
    }

    public function pdfdiploma(Track $track) {
        $user = Auth::user();

        $lessons = $track->lessons()->finished()->where('active', true)
            ->orderBy('order')->get();

        $data = [
            'track' => $track,
            'lessons' => $lessons,
            'user' => $user,
        ];

        $pdf = PDF::loadView('tracks.pdfdiploma', $data);

        return $pdf->download('diploma.pdf');
    }
}
