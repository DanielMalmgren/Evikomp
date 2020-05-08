<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TimeAttest extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function attestant(): BelongsTo
    {
        return $this->belongsTo('App\User', 'attestant_id', 'id');
    }

    public function scopeGender(Builder $query, string $gender): ?Builder
    {
        if($gender == 'M') {
            return $query->join('users', function($join)
            {
                $join->on('users.id', '=', 'time_attests.user_id')
                ->whereRaw('mod(substr(personid, 11, 1), 2)=1');
            });
        } elseif($gender == 'F') {
            return $query->join('users', function($join)
            {
                $join->on('users.id', '=', 'time_attests.user_id')
                ->whereRaw('mod(substr(personid, 11, 1), 2)=0');
            });
        } else {
            return null;
        }
    }

    protected $fillable = ['year', 'month', 'user_id', 'attestant_id', 'attestlevel', 'authnissuer', 'hours', 'clientip'];
}
