<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseOption extends Model
{
    use \Dimsav\Translatable\Translatable;

    public $translatedAttributes = ['text'];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }
}

class ResponseOptionTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['text'];
}
