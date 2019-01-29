<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkplaceType extends Model
{
    public function workplaces()
    {
        return $this->hasMany('App\Workplace');
    }
}
