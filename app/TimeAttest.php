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

    public function scopeGender($query, $gender)
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
