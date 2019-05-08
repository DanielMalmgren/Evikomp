<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\ActiveTime;
use App\ProjectTimeType;

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

    //Get this users workplace
    public function workplace()
    {
        return $this->belongsTo('App\Workplace');
    }

    //Get this users title
    public function title()
    {
        return $this->belongsTo('App\Title');
    }

    //Get all workplaces for which this user is admin
    public function admin_workplaces()
    {
        return $this->belongsToMany('App\Workplace', 'workplace_admins')->withPivot('attestlevel');
    }

    //Get this users locale
    public function locale()
    {
        return $this->belongsTo('App\Locale');
    }

    //Get all this users test sessions
    public function test_sessions()
    {
        return $this->hasMany('App\TestSession');
    }

    //Get all months that this user has closed (probably not very useful, just here for completeness)
    public function closed_months()
    {
        return $this->hasMany('App\ClosedMonth');
    }

    //Get all time attests made for this user
    public function time_attests()
    {
        return $this->hasMany('App\TimeAttest');
    }

    //Get all time attests made by this user
    public function time_attests_as_attestant()
    {
        return $this->hasMany('App\TimeAttest', 'attestant_id');
    }

    //Get all this users active times (ie times spent in this web platform)
    public function active_times()
    {
        return $this->hasMany('App\ActiveTime');
    }

    //Get all this users lesson results
    public function lesson_results()
    {
        return $this->hasMany('App\LessonResult');
    }

    //Get this users tracks
    public function tracks()
    {
        return $this->belongsToMany('App\Track');
    }

    //Get all project times registered on this user
    public function project_times()
    {
        return $this->belongsToMany('App\ProjectTime');
    }

    //Get all project times that this user has registered
    public function project_times_registered_by()
    {
        return $this->hasMany('App\ProjectTime', 'registered_by');
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
        $lesson_result = $this->lesson_results->sortBy('created_at')->last();
        if($lesson_result) {
            return $lesson_result->lesson;
        } else {
            return null;
        }
    }

    public function time_rows($year, $month) {
        $time_rows = [];
        $rowtitle = __('Tid i webappen');
        $monthtotal = 0;

        $total = 0;
        for($i = 1; $i <= 31; $i++) {
            $this_time = $active_times_db = ActiveTime::where('user_id', $this->id)->whereMonth('date', $month)->whereYear('date', $year)->whereDay('date', $i)->first();
            if($this_time) {
                $time_rows[$rowtitle][$i] = round($this_time->seconds/3600, 1);
                $total += round($this_time->seconds/3600, 1);
            }
        }
        $time_rows[$rowtitle][32] = $total;
        $monthtotal += $total;

        $types = $this->project_times()->whereMonth('date', $month)->whereYear('date', $year)->groupBy('project_time_type_id')->pluck('project_time_type_id');
        foreach($types as $type) {
            $rowtitle = ProjectTimeType::find($type)->name;
            $typetotal = 0;
            $dates = $this->project_times()->where('project_time_type_id', $type)->whereMonth('date', $month)->whereYear('date', $year)->groupBy('date')->pluck('date');
            foreach($dates as $date) {
                $occasions = $this->project_times()->where('project_time_type_id', $type)->where('date', $date)->get();
                $minutes = 0;
                foreach($occasions as $occasion) {
                     $minutes += $occasion->minutes();
                }
                $day = date('j', strtotime($date));
                $time_rows[$rowtitle][$day] = round($minutes/60, 1);
                $typetotal += round($minutes/60, 1);
            }
            $time_rows[$rowtitle][32] = $typetotal; //Use the 32th day of the month for the total. Maybe a bit ugly?
            $monthtotal += $typetotal;
        }

        $rowtitle = __('Summa');
        $time_rows[$rowtitle][32] = $monthtotal;

        return $time_rows;
    }

    //Get the next logical lesson for the user
    public function next_lesson()
    {
        $last_lesson = $this->last_lesson();
        if(!$last_lesson) {
            return Track::find(1)->first_lesson(); //If the user hasn't done any lessons yet, return the very first one
        }

        $next_lesson = $last_lesson->track->next_lesson($last_lesson);
        if($next_lesson) {
            return $next_lesson; //If this track has a next logical question, return it
        }
        $track = $this->tracks->merge($this->workplace->tracks)->sort()->where('id', '>', $last_lesson->track->id)->first();
        if($track) {
            return $track->first_lesson(); //Return the first lesson of the next track
        }
        return null; //Seems this user has done all lessons there is
    }

    public function setRememberToken($value)
    {
        //Override this function, doing noop since we don't use remember tokens
    }
}
