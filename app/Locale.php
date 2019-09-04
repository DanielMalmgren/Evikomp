<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    public function users()
    {
        return $this->hasMany('App\User');
    }

    public $incrementing = false;

    protected $keyType = 'string';
}
