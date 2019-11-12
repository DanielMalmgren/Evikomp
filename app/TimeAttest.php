<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeAttest extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function attestant()
    {
        return $this->belongsTo('App\User', 'attestant_id', 'id');
    }

    protected $fillable = ['year', 'month', 'user_id', 'attestant_id', 'attestlevel', 'authnissuer', 'hours', 'clientip'];
}
