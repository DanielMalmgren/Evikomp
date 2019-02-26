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

    public function minutes()
    {
        return ((new \DateTime($this->endtime))->getTimestamp() - (new \DateTime($this->starttime))->getTimestamp()) / 60;
    }
}
