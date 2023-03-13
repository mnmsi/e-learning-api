<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SaveCourse extends Model
{
    use HasFactory;

    protected $table = 'saved_courses';

    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'learner_id',
    ];

    protected $appends = [
        'is_save',
    ];

    public function getIsSaveAttribute()
    {
        return $this->learner_id == Auth::id() ? true : false;
    }

    protected $with = [
        'course',
    ];

    protected $withCount = [
        'students',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id')->without('videos', 'follow', 'educator', 'save_course');
    }

    public function students()
    {
        return $this->hasMany(CourseEnrolledStudent::class, 'learner_id', 'learner_id');
    }
}
