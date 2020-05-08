<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestSession extends Model
{
    public function lesson(): BelongsTo
    {
        return $this->belongsTo('App\Lesson');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function test_responses(): HasMany
    {
        return $this->hasMany('App\TestResponse');
    }

    public function number_of_questions(): int
    {
        return $this->lesson->questions->count();
    }

    public function completed_questions(): int
    {
        return $this->test_responses->where('correct', true)->count();
    }

    public function correct_on_first(): int
    {
        return $this->test_responses->where('wrong_responses', 0)->count();
    }

    public function percent(): int
    {
        return (int)round(100*($this->correct_on_first()/$this->number_of_questions()));
    }
}
