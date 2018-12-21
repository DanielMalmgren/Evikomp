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

    public function number_of_questions()
    {
        return $this->lesson->questions->count();
    }

    public function completed_questions()
    {
        return $this->test_responses->where('correct', true)->count();
    }

    public function correct_on_first() {
        return $this->test_responses->where('wrong_responses', 0)->count();
    }

    public function percent() {
        return round(100*($this->correct_on_first()/$this->number_of_questions()));
    }
}
