<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use \Dimsav\Translatable\Translatable;

    public $translatedAttributes = ['name', 'description'];

    public function tracks()
    {
        return $this->belongsToMany('App\Track');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

    public function test_sessions()
    {
        return $this->hasMany('App\TestSession');
    }

    public function lesson_results()
    {
        return $this->hasMany('App\LessonResult');
    }
}

class LessonTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
}
