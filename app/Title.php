<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    public function workplace_type()
    {
        return $this->belongsTo('App\WorkplaceType');
    }

    public function lessons()
    {
        return $this->belongsToMany('App\Lesson');
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
