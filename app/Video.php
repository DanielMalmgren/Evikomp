<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Video extends Model
{
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany('App\Lesson');
    }
}
