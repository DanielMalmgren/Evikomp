<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSetting extends Model
{
    use HasFactory;

    protected $fillable = ['content_id', 'key', 'value'];

    public function content()
    {
        return $this->belongsTo('App\Content');
    }

}
