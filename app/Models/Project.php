<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'course_id',
        'learner_id',
        'caption',
        'type',
        'media',
        'thumbnail',
        'is_private',
    ];

    protected $with = [
        'learner', 'course', 'learner_like',
    ];

    protected $withCount = [
        'project_like',
    ];

    protected $appends = [
        'media_path'
    ];

    public function getMediaPathAttribute()
    {
        if (!empty($this->media)) {
            return route('asset', ['data' => $this->media]);
        } else {
            return "";
        }
    }

    public function getThumbnailAttribute($value)
    {
        if (!empty($value)) {
            return route('asset', ['data' => $value]);
        } else {
            return "";
        }
    }

    public function learner()
    {
        return $this->belongsTo(User::class, 'learner_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')->without('videos');
    }

    public function project_like()
    {
        return $this->hasMany(ProjectLike::class, 'project_id', 'id');
    }

    public function learner_like()
    {
        return $this->hasOne(ProjectLike::class, 'project_id', 'id')->whereLearnerId(Auth::id());
    }
}
