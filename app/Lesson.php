<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;

class Lesson extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $translatedAttributes = ['name'];

    public function track()
    {
        return $this->belongsTo('App\Track');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

    public function test_sessions()
    {
        return $this->hasMany('App\TestSession');
    }

    public function lesson_results()
    {
        return $this->hasMany('App\LessonResult');
    }

    public function contents()
    {
        return $this->hasMany('App\Content');
    }

    public function titles()
    {
        return $this->belongsToMany('App\Title');
    }

    public function rating()
    {
        return $this->lesson_results->sum('rating');
    }

    public function getPagesAttribute()
    {
        return $this->contents->where('type', 'pagebreak')->count()+1;
    }

    function getFirstContentOnPage(int $page)
    {
        if($page==1) {
            return 0;
        }
        $pagebreak = $this->contents->where('type', 'pagebreak')->sortBy('order')->skip($page-2)->first();
        return $this->contents->where('order', $pagebreak->order+1)->first()->order;
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
}

class LessonTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
