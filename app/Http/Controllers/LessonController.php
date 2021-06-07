<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Track;
use App\Question;
use App\Title;
use App\LessonResult;
use App\Content;
use App\ContentSetting;
use App\Color;
use App\User;
use App\Poll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LessonController extends Controller
{
    public function show(Lesson $lesson, ?int $page=1): View {
        $user = Auth::user();
        $question = Question::where('lesson_id', $lesson->id)->first();
        $lesson->times_started++;
        $lesson->save();
        $data = [
            'question' => $question,
            'lesson' => $lesson,
            'page' => $page,
            'pages' => $lesson->pages,
            'my_lists' => $user->lesson_lists_owned,
            'first_content_order' => $lesson->getFirstContentOnPage($page),
            'is_editor' => $user->can('manage lessons') || Auth::user()->admin_tracks->where('id', $lesson->track->id)->isNotEmpty(),
        ];
        return view('lessons.show')->with($data);
    }

    public function replicate(Lesson $lesson): RedirectResponse {
        $newLesson = $lesson->replicateWithTranslations();
        $newLesson->times_started = 0;
        $newLesson->times_test_started = 0;
        $newLesson->times_finished = 0;
        $newLesson->active = 0;
        $newLesson->push();

        foreach($lesson->contents as $content) {
            $newContent = $content->replicateWithTranslations();
            $newContent->lesson_id = $newLesson->id;
            $newContent->push();
            if($newContent->type=='file' || $newContent->type=='image' || $newContent->type=='office' || $newContent->type=='audio') {
                Storage::copy($content->filepath().$content->filename(), $newContent->filepath().$newContent->filename());
            }
        }

        foreach($lesson->questions as $question) {
            $newQuestion = $question->replicateWithTranslations();
            $newQuestion->lesson_id = $newLesson->id;
            $newQuestion->push();
            foreach($question->response_options as $response_option) {
                $newOption = $response_option->replicateWithTranslations();
                $newOption->question_id = $newQuestion->id;
                $newOption->push();
            }
        }

        return redirect('/lessons/'.$newLesson->id)->with('success', __('Modulen har kopierats'));
    }

    public function create(Track $track): View {
        $titles = Title::all();
        $data = [
            'track' => $track,
            'titles' => $titles,
            'colors' => Color::all(),
            'polls' => Poll::all(),
        ];
        return view('lessons.create')->with($data);
    }

    public function store(Request $request): RedirectResponse {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
            'track' => 'required',
            'color' => 'exists:colors,hex',
            'icon' => 'image|max:2000',
        ],
        [
            'name.required' => __('Du måste ange ett namn på modulen!'),
            'color.exists' => __('Du måste välja en av de förvalda färgerna!'),
            'icon.image' => __('Felaktigt bildformat!'),
            'icon.max' => __('Din fil är för stor! Max-storleken är 2MB!'),
        ]);

        $currentLocale = \App::getLocale();

        $track = Track::find($request->track);

        $lesson = new Lesson();
        $lesson->track_id = $request->track;
        $lesson->order = $track->lessons->max('order')+1;
        $lesson->translateOrNew($currentLocale)->name = $request->name;
        $lesson->save();

        return $this->update($request, $lesson);
    }

    public function vote(Request $request, Lesson $lesson): void {
        $lesson_result = LessonResult::where([['user_id', '=', Auth::user()->id],['lesson_id', '=', $lesson->id]])->first();
        $lesson_result->rating = $request->vote;
        $lesson_result->save();
    }

    public function reorder(Request $request): void {
        parse_str($request->data, $data);
        $ids = $data['id'];

        foreach($ids as $order => $id){
            $lesson = Lesson::findOrFail($id);
            $lesson->order = $order+1;
            $lesson->save();
        }
    }

    public function edit(Lesson $lesson): View {
        $titles = Title::all();
        $data = [
            'lesson' => $lesson,
            'titles' => $titles,
            'tracks' => Track::all(),
            'colors' => Color::all(),
            'polls' => Poll::all(),
        ];
        return view('lessons.edit')->with($data);
    }

    public function editquestions(Lesson $lesson): View {
        $questions = $lesson->questions->sortBy('order');

        $data = [
            'lesson' => $lesson,
            'questions' => $questions,
            'lessonsWithQuestions' => Lesson::has('questions')->get(),
        ];
        return view('lessons.editquestions')->with($data);
    }

    public function replicateQuestions(Request $request): RedirectResponse {
        $this->validate($request, [
            'sourcelesson' => 'required',
            'targetlesson' => 'required',
        ]);

        $sourcelesson = Lesson::find($request->sourcelesson);
        $targetlesson = Lesson::find($request->targetlesson);

        logger("Replicating all questions from lesson ".$sourcelesson->id." to lesson ".$targetlesson->id.".");

        foreach($sourcelesson->questions as $question) {
            $newQuestion = $question->replicateWithTranslations();
            $newQuestion->lesson_id = $targetlesson->id;
            $newQuestion->push();
            foreach($question->response_options as $response_option) {
                $newOption = $response_option->replicateWithTranslations();
                $newOption->question_id = $newQuestion->id;
                $newOption->push();
            }
        }

        return redirect('/lessons/'.$targetlesson->id.'/editquestions');
    }

    /*public function finish(Lesson $lesson):RedirectResponse {

        $user = Auth::user();

        LessonResult::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id]
        );

        $lesson->send_notification($user);

        return redirect('/');
    }*/

    public function update(Request $request, Lesson $lesson): RedirectResponse {
        usleep(50000);
        $this->validate($request, [
            'name' => 'required',
            'track' => 'required',
            'new_audio.*' => 'file|mimetypes:audio/mpeg|max:61440',
            'new_audio' => 'array|max_uploaded_file_size:61440',
            'new_office.*' => 'file|mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation|max:10000',
            'new_image.*' => 'file|image|max:30000',
            'new_file.*' => 'file|max:30000',
            'new_vimeo.*' => 'integer',
            'vimeo.*' => 'integer',
            'color' => 'exists:colors,hex',
            'icon' => 'image|max:2000',
        ],
        [
            'name.required' => __('Du måste ange ett namn på modulen!'),
            'new_audio.*.mimetypes' => __('Din ljudfil måste vara i mp3-format!'),
            'new_office.*.mimetypes' => __('Din file måste vara antingen ett Word-dokument, en Excel-fil eller en Powerpoint-presentation!'),
            'new_image.*.image' => __('Felaktigt bildformat!'),
            'new_audio.*.file' => __('Du måste välja en fil att ladda upp!'),
            'new_office.*.file' => __('Du måste välja en fil att ladda upp!'),
            'new_image.*.file' => __('Du måste välja en fil att ladda upp!'),
            'new_file.*.file' => __('Du måste välja en fil att ladda upp!'),
            'new_audio.*.max' => __('Din fil är för stor! Max-storleken är 60MB!'),
            'new_office.*.max' => __('Din fil är för stor! Max-storleken är 10MB!'),
            'new_image.*.max' => __('Din fil är för stor! Max-storleken är 20MB!'),
            'new_file.*.max' => __('Din fil är för stor! Max-storleken är 20MB!'),
            'new_audio.max_uploaded_file_size' => __('För stora filer! Dina filer får totalt vara max 60MB!'),
            'new_vimeo.*.integer' => __('Ett giltigt Vimeo-id har bara siffror!'),
            'vimeo.*.integer' => __('Ett giltigt Vimeo-id har bara siffror!'),
            'color.exists' => __('Du måste välja en av de förvalda färgerna!'),
            'icon.image' => __('Felaktigt bildformat!'),
            'icon.max' => __('Din fil är för stor! Max-storleken är 2MB!'),
        ]);

        $currentLocale = \App::getLocale();
        $user = Auth::user();
        logger("Lesson ".$lesson->id." is being edited by ".$user->name);

        //Store this in a local variable. We'll have to replace all the temporary id's in it for real ones before we do the ordering
        $content_order = $request->content_order;

        $id_map = collect();

        //Loop through all changed html contents
        if($request->html) {
            foreach($request->html as $html_id => $html_text) {
                $content = Content::find($html_id);
                $newtext = Content::fix_links($html_text);
                if($content->translateOrNew($currentLocale)->text != $newtext) {
                    $content->translateOrNew($currentLocale)->text = $newtext;
                    $content->save();
                    logger("HTML content ".$html_id." is being changed");
                }
            }
        }

        //Loop through all added html contents
        if($request->new_html) {
            foreach($request->new_html as $temp_key => $new_html) {
                $newtext = Content::fix_links($new_html);
                $content = new Content('html', $lesson->id, null, $newtext);
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("HTML content ".$content->id." is being added");
            }
        }

        //Loop through all changed flipcard contents
        if($request->flipcard_front) {
            foreach($request->flipcard_front as $id => $front_text) {
                $back_text = $request->flipcard_back[$id];
                $content = Content::find($id);
                $newfront = str_replace(';', '', $front_text);
                $newback = str_replace(';', '', $back_text);
                if($content->translateOrNew($currentLocale)->text != $newfront.';'.$newback) {
                    $content->translateOrNew($currentLocale)->text = $newfront.';'.$newback;
                    logger("Flipcard content ".$id." is being changed");
                }
                $content->setColor($request->content_colors[$id]);
                $content->save();
            }
        }

        //Loop through all added flipcard contents
        if($request->new_flipcard_front) {
            foreach($request->new_flipcard_front as $temp_key => $front_text) {
                $back_text = $request->new_flipcard_back[$temp_key];
                $newfront = str_replace(';', '', Content::fix_links($front_text));
                $newback = str_replace(';', '', Content::fix_links($back_text));
                $content = new Content('flipcard', $lesson->id, null, $newfront.';'.$newback);
                $content->setColor($request->content_colors[$temp_key]);
                $content->save();
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("Flipcard content ".$content->id." is being added");
            }
        }

        //Loop through all changed vimeo contents
        if($request->vimeo) {
            foreach($request->vimeo as $vimeo_id => $vimeo_text) {
                $content = Content::find($vimeo_id);
                $content->content = $vimeo_text;
                $content->save();
                logger("Vimeo content ".$vimeo_id." is being changed");
            }
        }

        //Loop through all added vimeo contents
        if($request->new_vimeo) {
            foreach($request->new_vimeo as $temp_key => $new_vimeo) {
                $content = new Content('vimeo', $lesson->id, $new_vimeo);
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                $id_map->put($temp_key, $content->id);
                logger("Vimeo content ".$content->id." is being added");
            }
        }

        //Loop through all changed youtube contents
        if($request->youtube) {
            foreach($request->youtube as $youtube_id => $youtube_text) {
                $content = Content::find($youtube_id);
                $content->content = $youtube_text;
                $content->save();
                logger("Youtube content ".$youtube_id." is being changed");
            }
        }

        //Loop through all added youtube contents
        if($request->new_youtube) {
            foreach($request->new_youtube as $temp_key => $new_youtube) {
                $content = new Content('youtube', $lesson->id, $new_youtube);
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                $id_map->put($temp_key, $content->id);
                logger("Youtube content ".$content->id." is being added");
            }
        }

        //Loop through all added audio contents
        if($request->new_audio) {
            foreach($request->new_audio as $temp_key => $new_audio) {
                $content = new Content('audio', $lesson->id, null, $new_audio->getClientOriginalName());
                $new_audio->storeAs($content->filepath(true), $content->filename());
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("Audio content ".$content->id." is being added");
            }
        }

        //Loop through all added office contents
        if($request->new_office) {
            foreach($request->new_office as $temp_key => $new_office) {
                $content = new Content('office', $lesson->id, null, $new_office->getClientOriginalName());
                $new_office->storeAs($content->filepath(true), $content->filename());
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("Office content ".$content->id." is being added");
            }
        }

        //Loop through all added image files
        if($request->new_image) {
            foreach($request->new_image as $temp_key => $new_image) {
                $content = new Content('image', $lesson->id, null, $new_image->getClientOriginalName());
                $new_image->storeAs($content->filepath(true), $content->filename());
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                $id_map->put($temp_key, $content->id);
                logger("Image content ".$content->id." is being added");
            }
        }

        //Loop through all added file contents
        if($request->new_file) {
            foreach($request->new_file as $temp_key => $new_file) {
                $content = new Content('file', $lesson->id, null, $new_file->getClientOriginalName());
                $new_file->storeAs($content->filepath(true), $content->filename());
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("File content ".$content->id." is being added");
            }
        }

        //Loop through all changed content files
        if($request->replace_file) {
            foreach($request->replace_file as $content_id => $new_file) {
                logger("Image content ".$content_id." is being changed to ".$new_file);
                $content = Content::find($content_id);
                //logger("Deleting ".$content->filepath().$content->filename()." from disk");
                //Storage::delete($content->filepath().$content->filename());
                $content->translateOrNew($currentLocale)->text = $new_file->getClientOriginalName();
                $content->save();
                $new_file->storeAs($content->filepath(true), $content->filename());
            }
        }

        //Loop through all changed page breaks
        if($request->pagebreak) {
            foreach($request->pagebreak as $pagebreak_id => $pagebreak_text) {
                $content = Content::find($pagebreak_id);
                if($content->translateOrNew($currentLocale)->text != $pagebreak_text) {
                    logger("Page break ".$pagebreak_id." is being changed");
                }
                $content->translateOrNew($currentLocale)->text = $pagebreak_text;
                $content->setColor($request->content_colors[$pagebreak_id]);
                $content->save();
            }
        }

        //Loop through all added page breaks
        if($request->new_pagebreak) {
            foreach($request->new_pagebreak as $temp_key => $new_pagebreak) {
                $content = new Content('pagebreak', $lesson->id, null, $new_pagebreak);
                $content->setColor($request->content_colors[$temp_key]);
                $content->save();
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("Page break ".$content->id." is being added");
            }
        }

        //Loop through all added TOCs (table of contents)
        if($request->new_toc) {
            foreach($request->new_toc as $temp_key => $new_toc) {
                $content = new Content('toc', $lesson->id);
                $content->save();
                $content_order = str_replace("[".$temp_key."]", "[".$content->id."]", $content_order);
                logger("Table of contents ".$content->id." is being added");
            }
        }

        //Loop through all settings
        if($request->settings) {
            foreach($request->settings as $content_id => $settings) {
                foreach($settings as $key => $value) {
                    if(isset($value)) {
                        if($id_map->has($content_id)) {
                            ContentSetting::updateOrCreate(
                                ['content_id' => $id_map->get($content_id), 'key' => $key],
                                ['value' => $value]
                            );
                        } elseif(Content::find($content_id)) {
                            ContentSetting::updateOrCreate(
                                ['content_id' => $content_id, 'key' => $key],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
        }

        //Loop through all deleted contents
        if($request->remove_content) {
            foreach(array_keys($request->remove_content) as $content_id) {
                logger("Deleting content ".$content_id);
                Content::destroy($content_id);
            }
        }

        //Fix sort order of all contents
        $i = 0;
        if(strlen($content_order) > 0) {
            foreach(explode(",", $content_order) as $order) {
                preg_match('#\[(.*?)\]#', $order, $match); //Exctract the id, which is between []
                $id = $match[1];
                $content = Content::find($id);
                if($content) {
                    $content->order = $i;
                    $content->save();
                    $i++;
                }
            }
        }

        $color = Color::where('hex', $request->color)->first();
        $lesson->color_id = $color->id;

        if(isset($request->icon)) {
            $lesson->icon = basename($request->icon->store('public/icons'));
        }

        if($request->poll < 0) {
            $lesson->poll_id = null;
        } else {
            $lesson->poll_id = $request->poll;
        }

        if($request->diploma) {
            $lesson->diploma_layout = $request->diploma_layout;
            $lesson->diploma_require_all_track_lessons = $request->diploma_require_all_track_lessons;
        } else {
            $lesson->diploma_layout = null;
        }

        $lesson->translateOrNew($currentLocale)->name = $request->name;
        $lesson->active = $request->active;
        $lesson->track_id = $request->track;
        $lesson->limited_by_title = $request->limited_by_title;
        $lesson->save();

        $lesson->titles()->sync($request->titles);

        return redirect('/lessons/'.$lesson->id)->with('success', __('Ändringar sparade'));
    }

    public function destroy(Lesson $lesson): void {
        $user = Auth::user();
        logger("Lesson ".$lesson->id." is being removed by ".$user->name);
        foreach($lesson->contents as $content) {
            $content->delete();
        }
        $lesson->delete();
    }
}
