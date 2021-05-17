<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Track;
use App\User;
use App\Color;
use PDF;

class TrackController extends Controller
{
    public function index(Request $request) {
        if($request->showall) {
            if(Auth::user()->can('list all lessons')) {
                $tracks = Track::orderBy('order')->get();
            } else {
                $tracks = Track::where('active', 1)->get()->merge(Auth::user()->admin_tracks)->sortBy('order');
            }
        } else if(isset(Auth::user()->workplace)){
            $tracks = Auth::user()->tracks->merge(Auth::user()->workplace->tracks)->merge(Auth::user()->admin_tracks)->sortBy('order');
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
        if(empty(Auth::user()["workplace_id"]) || !Auth::user()->accepted_gdpr || empty(Auth::user()->email) || empty(Auth::user()->title)) {
            return redirect('/firstlogin');
        }

        if(Auth::user()->can('list all lessons') || Auth::user()->admin_tracks->contains($track)) {
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
            'is_editor' => Auth::user()->can('manage lessons') || Auth::user()->admin_tracks->where('id', $track->id)->isNotEmpty(),
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
        $data = [
            'colors' => Color::all(),
        ];
        
        return view('tracks.create')->with($data);
    }

    public function store(Request $request) {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
            'color' => 'exists:colors,hex',
            'icon' => 'image|max:2000',
        ],
        [
            'name.required' => __('Du måste ange ett namn på spåret!'),
            'color.exists' => __('Du måste välja en av de förvalda färgerna!'),
            'color.hex' => __('Du måste välja en av de förvalda färgerna!'),
            'icon.image' => __('Felaktigt bildformat!'),
            'icon.max' => __('Din fil är för stor! Max-storleken är 2MB!'),
        ]);

        $track = new Track();
        $track->id = Track::max('id')+1;
        $track->save();

        logger("Track ".$track->id." is being created by ".Auth::user()->name);

        return $this->update($request, $track);
    }

    public function edit(Track $track) {
        $data = [
            'track' => $track,
            'colors' => Color::all(),
        ];
        return view('tracks.edit')->with($data);
    }

    public function update(Request $request, Track $track) {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
            'color' => 'exists:colors,hex',
            'icon' => 'image|max:2000',
        ],
        [
            'name.required' => __('Du måste ange ett namn på spåret!'),
            'color.exists' => __('Du måste välja en av de förvalda färgerna!'),
            'color.hex' => __('Du måste välja en av de förvalda färgerna!'),
            'icon.image' => __('Felaktigt bildformat!'),
            'icon.max' => __('Din fil är för stor! Max-storleken är 2MB!'),
        ]);

        $currentLocale = \App::getLocale();
        $user = Auth::user();
        logger("Track ".$track->id." is being edited by ".$user->name);

        if($request->new_admins) {
            foreach($request->new_admins as $user_id) {
                $user = User::find($user_id);
                if(isset($user) && !$user->admin_tracks->contains($track)) {
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

        $color = Color::where('hex', $request->color)->first();
        $track->color_id = $color->id;

        if(isset($request->icon)) {
            $track->icon = basename($request->icon->store('public/icons'));
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
