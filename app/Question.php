<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use \Dimsav\Translatable\Translatable;

    public $translatedAttributes = ['text'];

    public function lesson()
    {
        return $this->belongsTo('App\Lesson');
    }

    public function testresponses()
    {
        return $this->hasMany('App\TestResponse');
    }
}

class QuestionTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['text'];
}
