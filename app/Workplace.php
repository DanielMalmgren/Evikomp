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
}
