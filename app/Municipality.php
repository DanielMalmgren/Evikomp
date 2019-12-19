<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    public function workplaces()
    {
        return $this->hasMany('App\Workplace');
    }

    public function total_attested_time($level) {
        $attested_time = 0;
        foreach($this->workplaces->filter() as $workplace) {
            $attested_time += $workplace->total_attested_time($level);
        }
        return $attested_time;
    }
}
