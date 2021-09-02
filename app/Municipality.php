<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Municipality extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    public function workplaces()
    {
        return $this->hasMany('App\Workplace');
    }

    public function time_attests()
    {
        return $this->hasManyDeep('App\TimeAttest', ['App\Workplace', 'App\User']);
    }

    public function users()
    {
        return $this->hasManyDeep('App\User', ['App\Workplace']);
    }

    public function scopeFilter(Builder $query)
    {
        return $query->whereRelation('workplaces', 'includetimeinreports', true);
    }
}
