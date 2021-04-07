<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Workplace extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany('App\User');
    }

    public function workplace_admins(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'workplace_admins')->withPivot('attestlevel');
    }

    public function polls(): BelongsToMany
    {
        return $this->belongsToMany('App\Poll', 'poll_workplace');
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo('App\Municipality');
    }

    public function workplace_type(): BelongsTo
    {
        return $this->belongsTo('App\WorkplaceType');
    }

    public function tracks(): BelongsToMany
    {
        return $this->belongsToMany('App\Track');
    }

    public function project_times(): HasMany
    {
        return $this->hasMany('App\ProjectTime')->orderBy('date')->orderBy('starttime');
    }

    public function color(): String
    {
        return '#'.substr(md5($this->id), 0, 6);
    }

    public function ptusers($month)
    {
        $users = collect();
        foreach ($this->project_times->where('month', $month) as $project_time) {
            $users = $users->merge($project_time->users);
        }
        return $users;
    }

    public function month_active_time(int $month, int $year): int {
        $active_time = 0;
        foreach($this->users as $user) {
            //logger('User '.$user->name.' has '.$user->active_time_minutes_month($month, $year).' active minutes');
            $active_time += $user->active_time_minutes_month($month, $year);
        }
        return $active_time;
    }

    public function month_attested_time(int $month, int $year, int $level): int {
        $attested_time = 0;
        foreach($this->users as $user) {
            $attested_time += $user->attested_time_month($month, $year, $level);
        }
        return $attested_time;

        //return $this->users->collapse()->time_attests->where('month', $month)->sum('hours');
    }

    public function total_attested_time(int $level): int {
        $attested_time = 0;
        foreach($this->users as $user) {
            $attested_time += $user->attested_time_total($level);
        }
        return $attested_time;
    }

    public function scopeFilter(Builder $query): Builder
    {
        return $query->where('includetimeinreports', true);
    }
}
