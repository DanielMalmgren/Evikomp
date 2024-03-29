<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Lesson;
use App\Interfaces\ModelInfo;

class User extends Authenticatable implements ModelInfo
{
    use \Lab404\Impersonate\Models\Impersonate;
    use HasFactory;

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

    //Get this users workplace
    public function workplace(): BelongsTo
    {
        return $this->belongsTo('App\Workplace');
    }

    //Get this users title
    public function title(): BelongsTo
    {
        return $this->belongsTo('App\Title');
    }

    //Get all workplaces for which this user is admin
    public function admin_workplaces(): BelongsToMany
    {
        return $this->belongsToMany('App\Workplace', 'workplace_admins')->withPivot('attestlevel');
    }

    //Get all tracks for which this user is admin
    public function admin_tracks(): BelongsToMany
    {
        return $this->belongsToMany('App\Track', 'track_admins')->withPivot('is_editor');
    }

    //Get all notification_receiver objects for lessons for which this user will receive notifications
    public function notification_receiver_for(): BelongsToMany
    {
        return $this->belongsToMany('App\NotificationReceiver', 'notification_receivers');
    }
    

    //Get this users locale
    public function locale(): BelongsTo
    {
        return $this->belongsTo('App\Locale');
    }

    //Get all this users test sessions
    public function test_sessions(): HasMany
    {
        return $this->hasMany('App\TestSession');
    }

    //Get all time attests that this user transferred from list
    public function time_attests_from_list(): HasMany
    {
        return $this->hasMany('App\TimeAttest');
    }

    //Get all months that this user has closed (probably not very useful, just here for completeness)
    public function closed_months(): HasMany
    {
        return $this->hasMany('App\ClosedMonth');
    }

    //Get all time attests made for this user
    public function time_attests(): HasMany
    {
        return $this->hasMany('App\TimeAttest');
    }

    //Get all time attests made by this user
    public function time_attests_as_attestant(): HasMany
    {
        return $this->hasMany('App\TimeAttest', 'attestant_id');
    }

    //Get all this users active times (ie times spent in this web platform)
    public function active_times(): HasMany
    {
        return $this->hasMany('App\ActiveTime');
    }

    //Get all lesson lists owned by this user
    public function lesson_lists_owned(): HasMany
    {
        return $this->hasMany('App\LessonList');
    }

    //Get all lesson lists assigned to this user
    public function lesson_lists()
    {
        return $this->morphToMany('App\LessonList', 'listable');
    }

    public function modelName(): String
    {
        return $this->name;
    }

    public function modelUrl(): String
    {
        return '/users/'.$this->id;
    }

    public function hasUrl(): bool
    {
        return true;
    }

    //Get all lesson lists assigned to this user or to this user's workplace
    public function all_lesson_lists(bool $include_own=false): Collection
    {
        $user_lists = $this->lesson_lists()->get();
        $workplace_lists = $this->workplace->lesson_lists()
                                ->where('user_id', '!=', $this->id)->get();
        if($include_own) {
            $own_lists = $this->lesson_lists_owned()->get();
            return $user_lists->merge($workplace_lists)->merge($own_lists)->sortBy('name');
        } else {
            return $user_lists->merge($workplace_lists)->sortBy('name');
        }
    }

    //Get all this users lesson results
    public function lesson_results(): HasMany
    {
        return $this->hasMany('App\LessonResult');
    }

    //Get this users tracks
    public function tracks(): BelongsToMany
    {
        return $this->belongsToMany('App\Track');
    }

    //Get all project times registered on this user
    public function project_times(): BelongsToMany
    {
        return $this->belongsToMany('App\ProjectTime');
    }

    //Get all project times that this user has registered
    public function project_times_registered_by(): HasMany
    {
        return $this->hasMany('App\ProjectTime', 'registered_by');
    }

    public function poll_sessions(): hasMany
    {
        return $this->hasMany('App\PollSession');
    }

    //Get all project times for which this user is the teacher
    public function taught_by(): HasMany
    {
        return $this->hasMany('App\ProjectTime', 'teacher_id');
    }

    public function canImpersonate()
    {
        return $this->hasRole('Admin');
    }
    
    public function active_time_today(): string
    {
        return date("H:i:s", $this->active_times->last()->seconds);
    }

    public function active_time_total(): string
    {
        return date("H:i:s", $this->active_times->sum('seconds')+59);
    }

    public function attested_time_month(int $month, int $year, int $level): float
    {
        return $this->time_attests->where('attestlevel', $level)->where('month', $month)->where('year', $year)->sum('hours');
    }

    public function month_is_fully_attested(int $year, int $month, float $breakpoint, int $level=1): bool
    {
        return $this->attested_time_month($month, $year, $level) + $breakpoint >= $this->month_total_time($year, $month);
    }

    public function attested_time_total(int $level): int
    {
        return $this->time_attests->where('attestlevel', $level)->sum('hours');
    }

    //Get the last lesson that this user did
    public function last_lesson(): ?Lesson
    {
        $lesson_result = $this->lesson_results->sortBy('created_at')->last();
        if($lesson_result) {
            return $lesson_result->lesson;
        } else {
            return null;
        }
    }

    public function month_total_time(int $year, int $month): float
    {
        $at_total = $this->active_times()
            ->where('date', '>=', $year.'-'.sprintf("%02d", $month).'-01')
            ->where('date', '<', ($month==12?$year+1:$year).'-'.sprintf("%02d", $month==12?1:$month+1).'-01')
            ->sum('seconds')/3600;

        $pt_total = 0;
        $dates = $this->project_times()->whereMonth('date', (string)$month)->whereYear('date', (string)$year)->groupBy('date')->pluck('date');
        foreach($dates as $date) {
            $occasions = $this->project_times()->where('date', $date)->get();
            $minutes = 0;
            foreach($occasions as $occasion) {
                $minutes += $occasion->minutes;
            }
            $pt_total += round($minutes/60, 1);
        }

        $monthtotal = $at_total + $pt_total;

        return $monthtotal;
    }

    public function time_rows(int $year, int $month): array
    {
        $time_rows = [];
        $rowtitle = __('Tid i lärplattformen');
        $monthtotal = 0;

        $total = 0;
        for($i = 1; $i <= 31; $i++) {
            $this_time = ActiveTime::where('user_id', $this->id)->whereMonth('date', (string)$month)->whereYear('date', (string)$year)->whereDay('date', (string)$i)->first();
            if($this_time) {
                $time_rows[$rowtitle][$i] = round($this_time->seconds/3600, 1);
                $total += round($this_time->seconds/3600, 1);
            }
        }
        $time_rows[$rowtitle][32] = $total;
        $monthtotal += $total;

        $types = $this->project_times()->whereMonth('date', (string)$month)->whereYear('date', (string)$year)->groupBy('project_time_type_id')->pluck('project_time_type_id');
        foreach($types as $type) {
            $rowtitle = ProjectTimeType::find($type)->name;
            $typetotal = 0;
            $dates = $this->project_times()->where('project_time_type_id', $type)->whereMonth('date', (string)$month)->whereYear('date', (string)$year)->groupBy('date')->pluck('date');
            foreach($dates as $date) {
                $occasions = $this->project_times()->where('project_time_type_id', $type)->where('date', $date)->get();
                $minutes = 0;
                foreach($occasions as $occasion) {
                     $minutes += $occasion->minutes;
                }
                $day = date('j', strtotime($date));
                $time_rows[$rowtitle][$day] = round($minutes/60, 1);
                $typetotal += round($minutes/60, 1);
            }
            $time_rows[$rowtitle][32] = $typetotal; //Use the 32th day of the month for the total. Maybe a bit ugly?
            $monthtotal += $typetotal;
        }

        $rowtitle = __('Summa');
        $time_rows[$rowtitle][32] = $monthtotal;

        return $time_rows;
    }

    //Get the next logical lesson for the user
    public function next_lesson(): ?Lesson
    {
        $last_lesson = $this->last_lesson();
        if(! $last_lesson) {
            return Track::find(1)->first_lesson(); //If the user hasn't done any lessons yet, return the very first one
        }

        $next_lesson = $last_lesson->track->next_lesson($last_lesson);
        if($next_lesson) {
            return $next_lesson; //If this track has a next logical question, return it
        }
        $track = $this->tracks->merge($this->workplace->tracks)->sort()->where('id', '>', $last_lesson->track->id)->first();
        if($track) {
            return $track->first_lesson(); //Return the first lesson of the next track
        }
        return null; //Seems this user has done all lessons there is
    }

    public function scopeFilter(Builder $query): Builder
    {
        return $query->join('workplaces', function($join)
            {
                $join->on('workplaces.id', '=', 'users.workplace_id')
                ->where('includetimeinreports', true);
            });
    }

    public function getGenderAttribute() {
        if(substr($this->personid, 10, 1) % 2 == '0') {
            return 'F';
        } else {
            return 'M';
        }
    }

    public function getBirthdateAttribute() {
        return substr($this->personid, 0, 4).'-'.substr($this->personid, 4, 2).'-'.substr($this->personid, 6, 2);
    }

    public function scopeGender(Builder $query, string $gender): ?Builder
    {
        if($gender == 'M') {
            return $query->whereRaw('mod(substr(personid, 11, 1), 2)=1');
        } elseif($gender == 'F') {
            return $query->whereRaw('mod(substr(personid, 11, 1), 2)=0');
        } else {
            return null;
        }
    }

    public function scopeGdpraccepted(Builder $query): Builder
    {
        return $query->where('accepted_gdpr', true);
    }

    public function setRememberToken($value)
    {
        //Override this function, doing noop since we don't use remember tokens
    }
}
