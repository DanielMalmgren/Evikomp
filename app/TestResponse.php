<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResponse extends Model
{
    public function test_session(): BelongsTo
    {
        return $this->belongsTo('App\TestSession');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo('App\Question');
    }

    protected $fillable = ['id', 'test_session_id', 'question_id'];
}
