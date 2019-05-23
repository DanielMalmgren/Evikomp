<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workplace extends Model
{
    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function workplace_admins()
    {
        return $this->belongsToMany('App\User', 'workplace_admins')->withPivot('attestlevel');
    }

    public function municipality()
    {
        return $this->belongsTo('App\Municipality');
    }

    public function workplace_type()
    {
        return $this->belongsTo('App\WorkplaceType');
    }

    public function tracks()
    {
        return $this->belongsToMany('App\Track');
    }

    public function project_times()
    {
        return $this->hasMany('App\ProjectTime');
    }

    public function month_active_time($month, $year) {
        $active_time = 0;
        foreach($this->users as $user) {
            //logger('User '.$user->name.' has '.$user->active_time_minutes_month($month, $year).' active minutes');
            $active_time += $user->active_time_minutes_month($month, $year);
        }
        return $active_time;
    }

    public function month_attested_time($month, $year, $level) {
        $attested_time = 0;
        foreach($this->users as $user) {
            $attested_time += $user->attested_time_month($month, $year, $level);
        }
        return $attested_time;

        //return $this->users->collapse()->time_attests->where('month', $month)->sum('hours');
    }
}
