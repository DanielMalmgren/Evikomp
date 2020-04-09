<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Config;

class Content extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $translatedAttributes = ['text'];

    function __construct($type=null, $lesson_id=null, $content=null, $text=null) {
        parent::__construct();
        if($type != null) {
            $currentLocale = \App::getLocale();
            $this->type = $type;
            $this->lesson_id = $lesson_id;
            $this->content = $content;
            if($text != null) {
                $this->translateOrNew($currentLocale)->text = $this->add_target_to_links($text);
            }
            $this->save();
        }
    }

    public static function boot () {
        parent::boot();

        self::deleting(function ($content) {
            Storage::deleteDirectory("public/files/".$content->id);
        });
    }

    //Return translated field if exists, else return value directly from contents table
    //TODO: fix this up once all files has been moved. Only the first line is needed!
    public function getExistingContentAttribute() {
        $translation = $this->translateOrDefault(\App::getLocale());
        if(isset($translation)) {
            return $translation->text;
        } else {
            return $this->content;
        }
    }

    public function lesson()
    {
        return $this->belongsTo('App\Lesson');
    }

    public function translatedFileExists() {
        return Storage::exists("public/files/".$this->id."/".\App::getLocale().'/'.$this->filename());
    }

    public function fallbackLangFileExists() {
        return Storage::exists("public/files/".$this->id."/".Config::get('app.fallback_locale').'/'.$this->filename());
    }

    public function filename() {
        return $this->existing_content;
    }

    //TODO: Remove this once all files has ben transferred to the new naming system!
    public function filename_oldstyle() {
        return $this->id.'.'.pathinfo($this->existing_content, PATHINFO_EXTENSION);
    }

    public function urlpath() {
        if($this->translatedFileExists()) {
            return "/storage/files/".$this->id."/".\App::getLocale().'/';
        } else if ($this->fallbackLangFileExists()) {
            return "/storage/files/".$this->id."/".Config::get('app.fallback_locale').'/';
        } else {
            return "/storage/files/";
        }
    }

    public function url() {
        if($this->translatedFileExists()) {
            return "/storage/files/".$this->id."/".\App::getLocale().'/'.$this->filename();
        } else if ($this->fallbackLangFileExists()) {
            return "/storage/files/".$this->id."/".Config::get('app.fallback_locale').'/'.$this->filename();
        } else {
            return "/storage/files/".$this->filename_oldstyle();
        }
    }

    public function filepath($ignoreMissing=false) {
        if($ignoreMissing || $this->translatedFileExists()) {
            return "public/files/".$this->id."/".\App::getLocale().'/';
        } else if ($this->fallbackLangFileExists()) {
            return "public/files/".$this->id."/".Config::get('app.fallback_locale').'/';
        } else {
            return "public/files/";
        }
    }

    public static function add_target_to_links($text) {
        return str_replace('<a href=', '<a target="_blank" href=', $text);
    }
}

class ContentTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['text'];
}
