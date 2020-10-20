<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Lesson;

class Track extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $translatedAttributes = ['name'];
    public $incrementing = false;

    public function workplaces(): BelongsToMany
    {
        return $this->belongsToMany('App\Workplace');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany('App\Lesson');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany('App\User');
    }

    public function track_admins(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'track_admins');
    }

    public function first_lesson(): ?Lesson
    {
        return $this->lessons()->orderBy('order')->where('active', true)->first();
    }

    public function next_lesson(Lesson $last_lesson): ?Lesson
    {
        return $this->lessons()->orderBy('order')->where('active', true)->where('order', '>', $last_lesson->order)->first();
    }
}

class TrackTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
