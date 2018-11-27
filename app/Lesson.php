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
}

class LessonTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
}
