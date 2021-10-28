<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Interfaces\ModelInfo;

class Workplace extends Model implements ModelInfo
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

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

    public function time_attests()
    {
        return $this->hasManyDeep('App\TimeAttest', ['App\User']);
    }

    public function active_times()
    {
        return $this->hasManyDeep('App\ActiveTime', ['App\User']);
    }

    //Get all lesson lists assigned to this workplace
    public function lesson_lists()
    {
        return $this->morphToMany('App\LessonList', 'listable');
    }

    public function modelName(): String
    {
        return $this->name;
    }

    public function modelUrl(): String
    {
        return '/workplace?workplace='.$this->id;
    }

    public function hasUrl(): bool
    {
        return true;
    }

    public function color(): String
    {
        return '#'.substr(md5((string)$this->id), 0, 6);
    }

    public function ptusers($month)
    {
        $users = collect();
        foreach ($this->project_times->where('month', $month) as $project_time) {
            $users = $users->merge($project_time->users);
        }
        return $users;

        //return $this->hasManyDeep('App\User', ['App\ProjectTime']);
    }

    public function month_active_time(int $month, int $year): int {
        return ($this->active_times->where('month', $month)->where('year', $year)->sum('seconds'))/60;
    }

    public function month_attested_time(int $month, int $year, int $level): float {
        return round($this->time_attests->where('attestlevel', $level)->where('month', $month)->where('year', $year)->sum('hours'), 1);
    }

    public function left_to_level_3_attest_previous_month(): float {
        $year = (int)date('Y', strtotime("first day of previous month"));
        $month = (int)date('n', strtotime("first day of previous month"));
        return $this->month_attested_time($month, $year, 1) - $this->month_attested_time($month, $year, 3);
    }

    public function scopeFilter(Builder $query): Builder
    {
        return $query->where('includetimeinreports', true);
    }
}
