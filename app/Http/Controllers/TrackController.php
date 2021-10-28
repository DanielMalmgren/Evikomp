<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Track;
use App\User;
use App\Color;
use App\LessonResult;
use App\Lesson;
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
        if(Auth::user()->can('list all lessons') || Auth::user()->admin_tracks->contains($track)) {
            $lessons = $track->lessons->sortBy('order');
        } else {
            $title = Auth::user()->title;

            $lessons = $track->lessons()->where('active', true)
                ->where(static function ($query) use ($title) {
                    $query->whereRelation('titles', 'id', $title->id)
                ->orWhere('limited_by_title', false);
                })
                ->orderBy('order')->get();

        }

        $data = [
            'track' => $track,
            'lessons' => $lessons,
            'is_editor' => Auth::user()->can('manage lessons') || Auth::user()->admin_tracks->where('id', $track->id)->where('pivot.is_editor', true)->isNotEmpty(),
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
        activity()->on($track)->log('created');

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
        activity()->on($track)->log('updated');

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

        if($request->new_factcheckers) {
            foreach($request->new_factcheckers as $user_id) {
                $user = User::find($user_id);
                if(isset($user) && !$user->admin_tracks->contains($track)) {
                    logger('Making '.$user->name.' fact checker for track '.$track->id);

                    $user->admin_tracks()->attach($track, ['is_editor' => false]);
                    $user->assignRole('Track editor');
                }
            }
        }

        if($request->remove_factchecker) {
            foreach(array_keys($request->remove_factchecker) as $user_id) {
                $user = User::find($user_id);
                logger('Removing '.$user->name.' as fact checker for track '.$track->id);
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

    //Download an Excel-fil with a compilation of all users that has completed anything in this track
    public function compilationXls(Track $track) {
        if(!Auth::user()->can('export track compilation')) {
            logger("User ".Auth::user()->id." is trying to get xls compilation for track ".$track->id.". Responding with http 403.");
            abort(403);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $worksheet = $spreadsheet->getActiveSheet();

        $cell = $worksheet->getCellByColumnAndRow(1, 1);
        $worksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        $cell = $worksheet->getCellByColumnAndRow(2, 1);
        $worksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

        $i = 3;
        foreach($track->lessons->where('active', true)->sortBy('order') as $lesson) {
            $lessonname = $lesson->translateOrDefault(\App::getLocale())->name;
            if(strlen($lessonname) > 10) {
                $lessonname = mb_substr($lessonname, 0, 9,'UTF-8')."…";
            }
            $cell = $worksheet->getCellByColumnAndRow($i, 1);
            $cell->setValue($lessonname);
            $worksheet->getColumnDimension($cell->getColumn())->setWidth(10);
            $worksheet->getStyle($cell->getCoordinate())->getFont()->setBold(true);
            $column_order[$lesson->id] = $i;
            $i++;
        }
        $cell = $worksheet->getCellByColumnAndRow($i, 1);
        $cell->setValue("Summa");
        $worksheet->getColumnDimension($cell->getColumn())->setWidth(8);
        $worksheet->getStyle($cell->getCoordinate())->getFont()->setBold(true);
        $column_order[-1] = $i;
        $i++;

        $cell = $worksheet->getCellByColumnAndRow($i, 1);
        $cell->setValue("Inloggad timmar");
        $worksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        $worksheet->getStyle($cell->getCoordinate())->getFont()->setBold(true);
        $column_order[-2] = $i;

        $row = 2;
        foreach($track->finished_users->sortBy('name') as $user) {
            if($user->isNot(Auth::user()) && ! Auth::user()->hasRole('Admin') && (! isset($user->workplace) || ! $user->workplace->workplace_admins->contains('id', Auth::user()->id))) {
                continue;
            }
            $total = 0;
            $worksheet->setCellValueByColumnAndRow(1, $row, $user->name);
            if($user->workplace) {
                $worksheet->setCellValueByColumnAndRow(2, $row, $user->workplace->name);
            }
            foreach($column_order as $lesson_id => $column) {
                if($lesson_id == -1) {
                    $cell = $worksheet->getCellByColumnAndRow($column, $row);
                    $cell->setValue($total);
                } elseif($lesson_id == -2) {
                    $cell = $worksheet->getCellByColumnAndRow($column, $row);
                    $cell->setValue(round($user->active_times->sum('seconds')/3600, 1));
                } elseif(LessonResult::where('user_id', $user->id)->where('lesson_id', $lesson_id)->exists()) {
                    $cell = $worksheet->getCellByColumnAndRow($column, $row);
                    $cell->setValue("✓");
                    $cell->getStyle()->getAlignment()->setHorizontal('center');
                    $total++;
                }
            }
            $row++;
        }

        $cell = $worksheet->getCellByColumnAndRow(1, $row);
        $cell->setValue("Summa");
        foreach($column_order as $lesson_id => $column) {
            if($lesson_id > 0) {
                $lesson = Lesson::find($lesson_id);
                $cell = $worksheet->getCellByColumnAndRow($column, $row);
                $cell->setValue($lesson->lesson_results->count());
                $cell->getStyle()->getAlignment()->setHorizontal('center');
            }
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = "Sammanställning Evikomp spår ".$track->translateOrDefault(\App::getLocale())->name.".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $writer->save("php://output");
    }
}
