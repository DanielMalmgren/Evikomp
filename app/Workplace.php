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
        return $this->belongsToMany('App\User', 'workplace_admins');
    }

    public function municipality()
    {
        return $this->belongsTo('App\Municipality');
    }

    public function workplace_type()
    {
        return $this->belongsTo('App\WorkplaceType');
    }

    public function title()
    {
        return $this->belongsTo('App\Title');
    }

    public function tracks()
    {
        return $this->belongsToMany('App\Track');
    }
}
