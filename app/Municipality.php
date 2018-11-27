<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    public function workplaces()
    {
        return $this->hasMany('App\Workplace');
    }
}
