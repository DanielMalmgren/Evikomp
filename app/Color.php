<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    public function tracks()
    {
        return $this->hasMany('App\Track');
    }

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }
}
