<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollQuestion extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $translatedAttributes = ['text', 'alternatives'];

    public function poll(): BelongsTo
    {
        return $this->belongsTo('App\Poll');
    }

    public function poll_response(): hasMany
    {
        return $this->hasMany('App\PollResponse');
    }

    public function next_question()
    {
        return $this->poll->next_question($this);
    }

    public function getAlternativesArrayAttribute()
    {
        return explode(';', $this->translateOrDefault(\App::getLocale())->alternatives);
    }
}

class PollQuestionTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['text', 'alternatives'];
}
