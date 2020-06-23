<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollResponse extends Model
{
    public function poll_session(): BelongsTo
    {
        return $this->belongsTo('App\PollSession');
    }

    public function poll_question(): BelongsTo
    {
        return $this->belongsTo('App\PollQuestion');
    }
}
