<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
