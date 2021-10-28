<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Interfaces\ModelInfo;

class TimeAttest extends Model implements ModelInfo
{
    use LogsActivity;

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function attestant(): BelongsTo
    {
        return $this->belongsTo('App\User', 'attestant_id', 'id');
    }

    public function project_time(): BelongsTo
    {
        return $this->belongsTo('App\ProjectTime');
    }

    public function from_list_by_user(): BelongsTo
    {
        return $this->belongsTo('App\User', 'from_list_by', 'id');
    }

    public function modelName(): String
    {
        return _('Attest för ').$this->user->name;
    }

    public function modelUrl(): String
    {
        return '';
    }

    public function hasUrl(): bool
    {
        return false;
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
