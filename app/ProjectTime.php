<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    /*public function minutes()
    {
        return ((new \DateTime($this->endtime))->getTimestamp() - (new \DateTime($this->starttime))->getTimestamp()) / 60;
    }*/

    public function getMinutesAttribute()
    {
        return ((new \DateTime($this->endtime))->getTimestamp() - (new \DateTime($this->starttime))->getTimestamp()) / 60;
    }

    public function getMonthAttribute()
    {
        return date("n", strtotime($this->date));
    }

    public function startstr() {
        return substr($this->starttime, 0, 5);
    }

    public function endstr() {
        return substr($this->endtime, 0, 5);
    }

}
