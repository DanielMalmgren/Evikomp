<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestResponse extends Model
{
    public function test_session()
    {
        return $this->belongsTo('App\TestSession');
    }

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    protected $fillable = ['id', 'test_session_id', 'question_id'];
}
