<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseEnrolledStudent extends Model
{
    use HasFactory;

    protected $table = 'course_enrolled_students';

    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'learner_id',
        'subscription_id',
    ];

    public function getCreatedAtAttribute($value)
    {
        if (!empty($value)) {
            return Carbon::make($value);
        } else {
            return "";
        }
    }

    protected $with = [
        'learner',
    ];

    protected $appends = [
        'is_enrolled',
    ];

    public function getIsEnrolledAttribute()
    {
        return $this->learner_id == Auth::id() ? true : false;
    }

    public function learner()
    {
        return $this->belongsTo(User::class, 'learner_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')->without('videos', 'follow', 'enroll');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'id');
    }

}
