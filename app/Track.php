<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $translatedAttributes = ['name'];
    public $incrementing = false;

    public function workplaces()
    {
        return $this->belongsToMany('App\Workplace');
    }

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function first_lesson()
    {
        return $this->lessons()->orderBy('order')->where('active', true)->first();
    }

    public function next_lesson($last_lesson)
    {
        return $this->lessons()->orderBy('order')->where('active', true)->where('order', '>', $last_lesson->order)->first();
    }
}

class TrackTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
