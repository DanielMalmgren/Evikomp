<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public function lessons()
    {
        return $this->belongsToMany('App\Lesson');
    }
}
