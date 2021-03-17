<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectTime extends Model
{
    public function workplace()
    {
        return $this->belongsTo('App\Workplace');
    }

    public function project_time_type()
    {
        return $this->belongsTo('App\ProjectTimeType');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function registered_by_user()
    {
        return $this->belongsTo('App\User', 'registered_by');
    }

    public function time_attests(): HasMany
    {
        return $this->hasMany('App\TimeAttest');
    }

    //Return the number of minutes for this project time
    public function getMinutesAttribute()
    {
        return ((new \DateTime($this->endtime))->getTimestamp() - (new \DateTime($this->starttime))->getTimestamp()) / 60;
    }

    //Return the number of minutes for this project time multiplied with the number of users affected by it
    public function getMinutesTotalAttribute()
    {
        return ((new \DateTime($this->endtime))->getTimestamp() - (new \DateTime($this->starttime))->getTimestamp()) / 60 * $this->users->count();
    }

    public function getMonthAttribute()
    {
        return date("n", strtotime($this->date));
    }

    public function getYearAttribute()
    {
        return date("Y", strtotime($this->date));
    }

    //Return true if any of the users connected to this project time has done any attest for the current month
    public function getIsAttestedAttribute()
    {
        foreach($this->users as $user) {
            if($user->time_attests->where('year', $this->year)->where('month', $this->month)->isNotEmpty()) {
                return true;
            }
        }
        return false;
    }

    public function startstr() {
        return substr($this->starttime, 0, 5);
    }

    public function endstr() {
        return substr($this->endtime, 0, 5);
    }

}
