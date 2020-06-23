<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\PollQuestion;

class Poll extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $translatedAttributes = ['name', 'infotext'];

    public function poll_sessions(): HasMany
    {
        return $this->hasMany('App\PollSession');
    }

    public function poll_questions(): HasMany
    {
        return $this->hasMany('App\PollQuestion');
    }

    public function municipalities(): BelongsToMany
    {
        return $this->belongsToMany('App\Municipality', 'poll_municipality');
    }

    //Return the first question in this poll
    public function first_question(): PollQuestion
    {
        return $this->poll_questions->sortBy('order')->first();
    }

    //Return the next question after a given question
    public function next_question(PollQuestion $question)
    {
        return $this->poll_questions->where('order', '>', $question->order)->sortBy('order')->first();
    }
}

class PollTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'infotext'];
}
