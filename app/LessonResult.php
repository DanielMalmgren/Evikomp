<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonResult extends Model
{
    public function lesson()
    {
        return $this->belongsTo('App\Lesson');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    protected $fillable = ['id', 'user_id', 'lesson_id'];
}
