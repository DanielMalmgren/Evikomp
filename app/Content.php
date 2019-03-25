<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use \Dimsav\Translatable\Translatable;

    public $translatedAttributes = ['text'];

    public function lesson()
    {
        return $this->belongsTo('App\Lesson');
    }
}


class ContentTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['text'];
}
