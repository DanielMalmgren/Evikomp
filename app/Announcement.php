<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Interfaces\ModelInfo;

class Announcement extends Model implements ModelInfo
{
    use LogsActivity;

    public function modelName(): String
    {
        return $this->heading;
    }

    public function modelUrl(): String
    {
        return '/announcements/'.$this->id;
    }

    public function hasUrl(): bool
    {
        return true;
    }
}
