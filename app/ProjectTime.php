<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\LessonResult;

class ProjectTime extends Model
{
    //The workplace having this project time
    public function workplace(): BelongsTo
    {
        return $this->belongsTo('App\Workplace');
    }

    //The type of project time
    public function project_time_type(): BelongsTo
    {
        return $this->belongsTo('App\ProjectTimeType');
    }

    //The users having this project time
    public function users(): BelongsToMany
    {
        return $this->belongsToMany('App\User');
    }

    //The user registering this project time
    public function registered_by_user(): BelongsTo
    {
        return $this->belongsTo('App\User', 'registered_by');
    }

    //The time attests connected to this project time (only used if atttesting from presence list)
    public function time_attests(): HasMany
    {
        return $this->hasMany('App\TimeAttest');
    }

    //The teacher assigned to this lesson
    public function teacher(): BelongsTo
    {
        return $this->belongsTo('App\User', 'teacher_id');
    }

    //The training coordinator organization responsible for assigning a teacher
    public function training_coordinator(): BelongsTo
    {
        return $this->belongsTo('App\Workplace', 'training_coordinator_id');
    }

    //The lessons the workplace wants to be taught this project time
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany('App\Lesson');
    }

    //Return the color of this project time (in hex), which depends on it's status
    public function color()
    {
        if($this->cancelled) {
            return '#ffa500';
        } elseif($this->no_teacher_available) {
            return '#7d3c98';
        } elseif($this->need_teacher && $this->teacher_id === null) {
            return '#ff0000';
        } else {
            return '#00ff00';
        }
    }

    //Return the number of minutes for this project time
    public function getMinutesAttribute()
    {
        return ((new \DateTime($this->endtime))->getTimestamp() - (new \DateTime($this->starttime))->getTimestamp()) / 60;
    }

    //Return the number of minutes for this project time multiplied with the number of users affected by it
    public function getMinutesTotalAttribute()
    {
        return ((new \DateTime($this->endtime))->getTimestamp() - (new \DateTime($this->starttime))->getTimestamp()) / 60 * $this->users->count();
    }

    public function getMonthAttribute()
    {
        return date("n", strtotime($this->date));
    }

    public function getYearAttribute()
    {
        return date("Y", strtotime($this->date));
    }

    //Return true if any of the users connected to this project time has done any attest for the current month
    public function getIsAttestedAttribute()
    {
        foreach($this->users as $user) {
            if($user->time_attests->where('year', $this->year)->where('month', $this->month)->isNotEmpty()) {
                return true;
            }
        }
        return false;
    }

    public function startstr() {
        return substr($this->starttime, 0, 5);
    }

    public function endstr() {
        return substr($this->endtime, 0, 5);
    }

    //Confirm that this project time has occurred and that all attached users should get the lessons involved marked as finished
    public function confirm() {
        foreach($this->lessons as $lesson) {
            foreach($this->users as $user) {
                logger("Marking lesson ".$lesson->id." as finished for ".$user->name);
                LessonResult::FirstOrCreate(
                    ['user_id' => $user->id, 'lesson_id' => $lesson->id]
                );

            }
        }
    }

    //Notify this project time's training coordinator
    //Type new means they should assign a teacher
    //Type cancelled is a notification about cancellation
    public function notify_training_coordinator($type = 'new') {
        foreach($this->training_coordinator->workplace_admins as $user) {
            $to = [];
            $to[] = ['email' => $user->email, 'name' => $user->name];

            try {
                switch($type) {
                    case 'new':
                        \Mail::to($to)->send(new \App\Mail\ChooseTeacherNotification($this));
                        logger("Sent teacher notification mail to ".$user->email);
                        break;
                    case 'cancelled':
                        \Mail::to($to)->send(new \App\Mail\ProjectTimeCancelledNotification($this));
                        logger("Sent cancellation notification mail to ".$user->email);
                        break;
                    default:
                        logger("notify_training_coordinator called with wrong type argument!");
                }
            } catch(\Swift_TransportException $e) {
                logger("Couldn't send mail to ".$user->email);
            }
        }
    }

}
