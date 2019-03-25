<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use \Dimsav\Translatable\Translatable;

    public $translatedAttributes = ['name'];

    public function track()
    {
        return $this->belongsTo('App\Track');
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

    public function contents()
    {
        return $this->hasMany('App\Content');
    }

    public function titles()
    {
        return $this->belongsToMany('App\Title');
    }

    public function rating()
    {
        return $this->lesson_results->sum('rating');
    }
}

class LessonTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
