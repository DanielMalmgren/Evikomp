<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkplaceType extends Model
{
    public function workplaces(): HasMany
    {
        return $this->hasMany('App\Workplace');
    }

    public function titles(): HasMany
    {
        return $this->hasMany('App\Title');
    }
}
