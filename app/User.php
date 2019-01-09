<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function workplace()
    {
        return $this->belongsTo('App\Workplace');
    }

    public function locale()
    {
        return $this->belongsTo('App\Locale');
    }

    public function test_session()
    {
        return $this->hasMany('App\TestSession');
    }

    public function lesson_results()
    {
        return $this->hasMany('App\LessonResult');
    }

    public function tracks()
    {
        return $this->belongsToMany('App\Track');
    }
}
