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

    public function title()
    {
        return $this->belongsTo('App\Title');
    }

    public function admin_workplaces()
    {
        return $this->belongsToMany('App\Workplace', 'workplace_admins');
    }

    public function locale()
    {
        return $this->belongsTo('App\Locale');
    }

    public function test_sessions()
    {
        return $this->hasMany('App\TestSession');
    }

    public function active_times()
    {
        return $this->hasMany('App\ActiveTime');
    }

    public function lesson_results()
    {
        return $this->hasMany('App\LessonResult');
    }

    public function tracks()
    {
        return $this->belongsToMany('App\Track');
    }

    public function project_times()
    {
        return $this->belongsToMany('App\ProjectTime');
    }

    public function active_time_today()
    {
        return date("H:i:s", $this->active_times->last()->seconds);
    }

    public function active_time_total()
    {
        return date("H:i:s", $this->active_times->sum('seconds')+59);
    }

    //Get the last lesson that this user did
    public function last_lesson()
    {
        return $this->lesson_results->orderBy('created_at')->first()->get();
    }

    public function setRememberToken($value)
    {
        //Override this function, doing noop since we don't use remember tokens
    }
}
