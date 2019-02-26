<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTimeType extends Model
{
    public function project_timess()
    {
        return $this->hasMany('App\ProjectTime');
    }
}
