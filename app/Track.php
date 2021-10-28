<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Lesson;
use App\Interfaces\ModelInfo;

class Track extends Model implements ModelInfo
{
    use \Astrotomic\Translatable\Translatable;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    public $translatedAttributes = ['name'];
    public $incrementing = false;

    public function workplaces(): BelongsToMany
    {
        return $this->belongsToMany('App\Workplace');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany('App\Lesson');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany('App\User');
    }

    public function track_admins(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'track_admins')->withPivot('is_editor');
    }

    public function modelName(): String
    {
        return $this->translateOrDefault(\App::getLocale())->name;
    }

    public function modelUrl(): String
    {
        return '/tracks/'.$this->id;
    }

    public function hasUrl(): bool
    {
        return true;
    }

    //Returns all users that has finished any module within this track
    public function finished_users() {
        return $this->hasManyDeep('App\User', ['App\Lesson', 'lesson_results'])->groupBy('users.id');
    }

    public function color()
    {
        return $this->belongsTo('App\Color')->withDefault([
            'hex' => '#ffffff',
        ]);
    }

    public function icon_with_path()
    {
        if(isset($this->icon) && $this->icon != '') {
            return "/storage/icons/".$this->icon;
        } else {
            return "/storage/icons/default.png";
        }
    }

    public function first_lesson(): ?Lesson
    {
        return $this->lessons()->orderBy('order')->where('active', true)->first();
    }

    public function next_lesson(Lesson $last_lesson): ?Lesson
    {
        return $this->lessons()->orderBy('order')->where('active', true)->where('order', '>', $last_lesson->order)->first();
    }
}

class TrackTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
