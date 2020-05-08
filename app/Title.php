<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Title extends Model
{
    public function workplace_type(): BelongsTo
    {
        return $this->belongsTo('App\WorkplaceType');
    }

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany('App\Lesson');
    }

    public function users(): HasMany
    {
        return $this->hasMany('App\User');
    }
}
