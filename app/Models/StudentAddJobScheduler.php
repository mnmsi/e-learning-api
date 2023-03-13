<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAddJobScheduler extends Model
{
    use HasFactory;

    protected $table = 'student_add_job_schedulers';

    protected $fillable = [
        'course_id',
        'file',
        'status'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class)
                    ->without( 'videos', 'follow', 'enroll', 'reviews', 'total_videos', 'watch_history', 'save_course');
    }
}
