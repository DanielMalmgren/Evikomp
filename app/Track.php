<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use \Dimsav\Translatable\Translatable;

    public $translatedAttributes = ['name'];

    public function workplaces()
    {
        return $this->belongsToMany('App\Workplace');
    }

    public function lessons()
    {
        return $this->belongsToMany('App\Lesson');
    }
}

class TrackTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
