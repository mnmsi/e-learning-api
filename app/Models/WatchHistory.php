<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    use HasFactory;

    protected $table = 'watch_history';

    protected $fillable = [
        'course_id',
        'video_id',
        'learner_id',
        'duration',
        're_watch_duration',
        'is_complete'
    ];
}
