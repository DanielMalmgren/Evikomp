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

    public function scopeFinished($query, User $user=null)
    {
        if(!$user) {
            $user = Auth::user();
        }
        return $query->join('lesson_results', function($join) use($user)
            {
                $join->on('lesson_results.lesson_id', '=', 'lessons.id')
                ->where("user_id", $user->id);
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
