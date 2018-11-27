<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workplace extends Model
{
    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function municipality()
    {
        return $this->belongsTo('App\Municipality');
    }

    public function tracks()
    {
        return $this->belongsToMany('App\Track');
    }
}
