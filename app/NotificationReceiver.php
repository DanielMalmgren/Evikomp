<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NotificationReceiver extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'lesson_id', 'workplace_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function workplace(): BelongsTo
    {
        return $this->belongsTo('App\Workplace');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo('App\Lesson');
    }
}
