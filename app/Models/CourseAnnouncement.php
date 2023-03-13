<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAnnouncement extends Model
{
    use HasFactory;

    protected $table = 'course_announcements';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'media_path',
        'thumbnail_path',
    ];

    protected $appends = [
        'media_url',
        'thumbnail_url',
    ];

    public function getMediaUrlAttribute()
    {
        return route('asset', ['data' => $this->media_path]);
    }

    public function getThumbnailUrlAttribute()
    {
        return route('asset', ['data' => $this->thumbnail_path]);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}
