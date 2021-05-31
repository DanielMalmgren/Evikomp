<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class LessonList extends Model
{
    use HasFactory;

    //Get this lesson list's owner
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    //Get all lessons in this lesson list
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany('App\Lesson');
    }
    
    //Get all users assigned to this list
    public function users(): MorphToMany
    {
        return $this->morphedByMany('App\User', 'listable');
    }

    //Get all workplaces assigned to this list
    public function workplaces(): MorphToMany
    {
        return $this->morphedByMany('App\Workplace', 'listable');
    }
}
