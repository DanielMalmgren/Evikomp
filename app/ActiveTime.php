<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActiveTime extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getTimeAttribute($value)
    {
        return new \DateTime('0000-00-00 ' . $value);
    }

    public function getMonthAttribute()
    {
        return date("n", strtotime($this->date));
    }

    public function getYearAttribute()
    {
        return date("Y", strtotime($this->date));
    }

    protected $fillable = ['user_id', 'date'];

    public function scopeFilter($query)
    {
        return $query->join('users', function($join)
            {
                $join->on('users.id', '=', 'active_times.user_id');
            })->join('workplaces', function($join)
            {
                $join->on('workplaces.id', '=', 'users.workplace_id')
                ->where('includetimeinreports', true);
            });
    }

    //protected $dates = ['created_at', 'updated_at', 'time'];
}
