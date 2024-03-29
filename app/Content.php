<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Config;
use App\ContentSetting;

class Content extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $translatedAttributes = ['text'];

    function __construct($type=null, $lesson_id=null, $content=null, $text=null, $order=null) {
        parent::__construct();
        if($type != null) {
            $currentLocale = \App::getLocale();
            $this->type = $type;
            $this->lesson_id = $lesson_id;
            $this->content = $content;
            $this->order = $order;
            if($text != null) {
                $this->translateOrNew($currentLocale)->text = $this->fix_links($text);
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

    public function lesson()
    {
        return $this->belongsTo('App\Lesson');
    }

    public function color()
    {
        return $this->belongsTo('App\Color')->withDefault([
            'hex' => '#ffffff',
        ]);
    }

    public function content_settings()
    {
        return $this->hasMany('App\ContentSetting');
    }

    public function getHashAttribute()
    {
        $valueobj = $this->content_settings->where('key', 'hash')->first();
        if(isset($valueobj)) {
            return $valueobj->value;
        } else {
            return '';
        }
    }

    public function getHashForEmbeddingAttribute()
    {
        $valueobj = $this->content_settings->where('key', 'hash')->first();
        if(isset($valueobj)) {
            return '?h='.$valueobj->value;
        } else {
            return '';
        }
    }

    public function getMaxWidthAttribute()
    {
        $valueobj = $this->content_settings->where('key', 'max_width')->first();
        if(isset($valueobj)) {
            return $valueobj->value;
        } else {
            return '250';
        }
    }

    public function getMaxHeightAttribute()
    {
        $valueobj = $this->content_settings->where('key', 'max_height')->first();
        if(isset($valueobj)) {
            return $valueobj->value;
        } else {
            return '250';
        }
    }


    public function getAdjustmentAttribute()
    {
        $valueobj = $this->content_settings->where('key', 'adjustment')->first();
        if(isset($valueobj)) {
            return $valueobj->value;
        } else {
            return 'left';
        }
    }

    public function getTextAttribute()
    {
        $translation = $this->translateOrDefault(\App::getLocale());
        if(isset($translation)) {
            return $translation->text;
        } else {
            return '';
        }
    }

    public function getSummaryAttribute()
    {
        $summary = '';

        switch ($this->type) {
            case 'html':
                $summary = strip_tags($this->text);
                break;
            case 'pagebreak':
                $summary = $this->text;
                break;
            case 'image':
            case 'audio':
            case 'file':
            case 'office':
                $summary = $this->filename();
                break;
            case 'vimeo':
            case 'youtube':
                $summary = $this->content;
                break;
        }

        if(!isset($summary)) {
            return '';
        } elseif(strlen($summary) < 50) {
            return $summary;
        } else {
            return mb_substr($summary, 0, 47)."...";
        }
    }

    public function setColor(String $hex) {
        $color = Color::where('hex', $hex)->first();
        if(isset($color)) {
            $this->color_id = $color->id;
        }
    }

    public function textPart($part) {
        $translation = $this->translateOrDefault(\App::getLocale());
        if(isset($translation)) {
            return explode(';', $translation->text)[$part];
        } else {
            return '';
        }
    }

    public function translatedFileExists() {
        return Storage::exists("public/files/".$this->id."/".\App::getLocale().'/'.$this->filename());
    }

    public function filename() {
        $translation = $this->translateOrDefault(\App::getLocale());
        if(isset($translation)) {
            return $translation->text;
        } else {
            return 'broken.png';
        }
    }

    public function urlpath() {
        if($this->translatedFileExists()) {
            return "/storage/files/".$this->id."/".\App::getLocale().'/';
        } else {
            return "/storage/files/".$this->id."/".Config::get('app.fallback_locale').'/';
        }
    }

    public function url() {
        if($this->translatedFileExists()) {
            return "/storage/files/".$this->id."/".\App::getLocale().'/'.$this->filename();
        } else {
            return "/storage/files/".$this->id."/".Config::get('app.fallback_locale').'/'.$this->filename();
        }
    }

    public function getTextIfExists() {
        if($this->translateOrDefault(\App::getLocale()) !== null) {
            return $this->translateOrDefault(\App::getLocale())->text;
        } else {
            return null;
        }
    }

    public function filepath($ignoreMissing=false) {
        if($ignoreMissing || $this->translatedFileExists()) {
            return "public/files/".$this->id."/".\App::getLocale().'/';
        } else {
            return "public/files/".$this->id."/".Config::get('app.fallback_locale').'/';
        }
    }

    public static function fix_links($text) {
        $text = str_replace('<a href=', '<a target="_blank" href=', $text);
        $text = preg_replace('/(^|[\n\s])#([^\s"\t\n\r<:]*)/is', '$1<a target="" href="/tags/$2">#$2</a>', $text);
        return $text;
    }
}

class ContentTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['text'];
}
