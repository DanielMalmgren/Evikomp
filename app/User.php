<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function workplace()
    {
        return $this->belongsTo('App\Workplace');
    }

    public function title()
    {
        return $this->belongsTo('App\Title');
    }

    public function admin_workplaces()
    {
        return $this->belongsToMany('App\Workplace', 'workplace_admins');
    }

    public function locale()
    {
        return $this->belongsTo('App\Locale');
    }

    public function test_sessions()
    {
        return $this->hasMany('App\TestSession');
    }

    public function active_times()
    {
        return $this->hasMany('App\ActiveTime');
    }

    public function lesson_results()
    {
        return $this->hasMany('App\LessonResult');
    }

    public function tracks()
    {
        return $this->belongsToMany('App\Track');
    }

    public function project_times()
    {
        return $this->belongsToMany('App\ProjectTime');
    }

    public function active_time_today()
    {
        return date("H:i:s", $this->active_times->last()->seconds);
    }

    public function active_time_total()
    {
        return date("H:i:s", $this->active_times->sum('seconds')+59);
    }

    //Get the last lesson that this user did
    public function last_lesson()
    {
        $lesson_result = $this->lesson_results->sortBy('created_at')->first();
        if($lesson_result) {
            return $lesson_result->lesson;
        } else {
            return null;
        }
    }

    //Get the next logical lesson for the user
    public function next_lesson()
    {
        $last_lesson = $this->last_lesson();
        if(!$last_lesson) {
            return Track::find(1)->first_lesson(); //If the user hasn't done any lessons yet, return the very first one
        }
        logger("Senaste lektionen: ".$last_lesson->name);
        $next_lesson = $last_lesson->track->next_lesson($last_lesson);
        if($next_lesson) {
            return $next_lesson; //If this track has a next logical question, return it
        }
        $track = $this->tracks->merge($this->workplace->tracks)->sort()->where('id', '>', $last_lesson->track->id)->first();
        if($track) {
            logger("Nästa spår: ".$track->name);
            return $track->first_lesson(); //Return the first lesson of the next track
        }
        return null; //Seems this user has done all lessons there is
    }

    public function setRememberToken($value)
    {
        //Override this function, doing noop since we don't use remember tokens
    }
}
