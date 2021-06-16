<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Support\Facades\Auth;

class Lesson extends Model
{
    use \Astrotomic\Translatable\Translatable;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    public $translatedAttributes = ['name'];

    public function track(): BelongsTo
    {
        return $this->belongsTo('App\Track');
    }

    public function questions(): HasMany
    {
        return $this->hasMany('App\Question');
    }

    public function test_sessions(): HasMany
    {
        return $this->hasMany('App\TestSession');
    }

    public function lesson_results(): HasMany
    {
        return $this->hasMany('App\LessonResult');
    }

    public function contents(): HasMany
    {
        return $this->hasMany('App\Content');
    }

    public function titles(): BelongsToMany
    {
        return $this->belongsToMany('App\Title');
    }

    public function lesson_lists(): BelongsToMany
    {
        return $this->belongsToMany('App\LessonList')->withPivot('order');
    }

    public function notification_receivers(): HasMany
    {
        return $this->hasMany('App\NotificationReceiver');
    }

    public function poll(): BelongsTo
    {
        return $this->belongsTo('App\Poll');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo('App\Color')->withDefault([
            'hex' => '#ffffff',
        ]);
    }

    public function project_times(): BelongsToMany
    {
        return $this->belongsToMany('App\ProjectTime');
    }

    public function rating()
    {
        return $this->lesson_results->sum('rating');
    }

    public function getPagesAttribute()
    {
        return $this->contents->where('type', 'pagebreak')->count();
    }

    public function page_heading(int $page)
    {
        $content_locale = $this->contents->where('type', 'pagebreak')->sortBy('order')->skip($page-1)->first()->translateOrDefault(\App::getLocale());
        if(isset($content_locale)) {
            return $content_locale->text;
        } else {
            return __('Sida ').$page;
        }
    }

    public function page_color_style(int $page)
    {
        $hex = $this->contents->where('type', 'pagebreak')->sortBy('order')->skip($page-1)->first()->color->hex;
        
        if($hex == "#ffffff") { //White means default color should be used
            return "";
        } else {
            return "background-color:".$hex.";border-color:".$hex;
        }
    }

    function getFirstContentOnPage(int $page)
    {
        $pagebreak = $this->contents->where('type', 'pagebreak')->sortBy('order')->skip($page-1)->first();
        if(isset($pagebreak)) {
            $firstcontent = $this->contents->where('order', $pagebreak->order+1)->first();
            if(isset($firstcontent)) {
                return $firstcontent->order;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function scopeFinished($query, User $user=null)
    {
        if(!$user) {
            $user = Auth::user();
        }
        return $query->whereHas('lesson_results', function($query) use($user)
            {
                $query->where("user_id", $user->id);
            });
    }

    //Returns whether a particular user has finished this lesson.
    public function isFinished(User $user=null) {
        if(!$user) {
            $user = Auth::user();
        }
        return $this->lesson_results->where("user_id", $user->id)->isNotEmpty();
    }

    //Send mail to all notification receivers telling them that $user has finished the lesson
    //TODO: This should probably happen asynchronously to minimize delay for the user
    public function send_notification(User $user) {
        if($this->notification_receivers->where('workplace_id', $user->workplace_id)->isNotEmpty()) {
            foreach($this->notification_receivers->where('workplace_id', $user->workplace_id) as $receiver) {
                logger("Preparing email to ".$receiver->user->name." (".$receiver->user->email.")");
                $to = [];
                $to[] = ['email' => $receiver->user->email, 'name' => $receiver->user->name];
                setlocale(LC_NUMERIC, $receiver->user->locale_id);

                $finished_lessons = $this->track->lessons->whereIn('id', $user->lesson_results->pluck('lesson_id'));

                try {
                    \Mail::to($to)->send(new \App\Mail\LessonNotification($user, $this, $finished_lessons));
                    logger("  Mail sent");
                } catch(\Swift_TransportException $e) {
                    logger("  Sending failed!");
                }
            }
        }
    }
}

class LessonTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
