<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    public function lesson()
    {
        return $this->belongsTo('App\Lesson');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function test_responses()
    {
        return $this->hasMany('App\TestResponse');
    }
}
