<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    public function tracks()
    {
        return $this->hasMany('App\Track');
    }

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }

    public function contents()
    {
        return $this->hasMany('App\Content');
    }

    public function hex_without_hash()
    {
        return substr($this->hex, 1);
    }

    public static function list_for_trumbowyg()
    {
        $list = '';
        foreach(Color::all() as $color) {
            $list .= "'".$color->hex_without_hash()."', ";
        }
        return $list;
    }
}
