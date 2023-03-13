<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';

    protected $fillable = [
        'course_id',
        'educator_id',
        'title',
        'description',
        'privacy',
        'location',
        'subscription_type',
        'video_thumbnail',
        'video_url',
        'duration',
        'order_no',
    ];

    protected $appends = [
        'video_path'
    ];

    public function getVideoThumbnailAttribute($value)
    {
        if (!empty($value)) {
            return route('asset', ['data' => $value]);
        } else {
            return "";
        }
    }

    public function getVideoPathAttribute()
    {
        if (!empty($this->video_url)) {
            return route('asset', ['data' => $this->video_url]);
        } else {
            return "";
        }
    }

    public function educator()
    {
        return $this->belongsTo(User::class, 'educator_id');
    }

    public function question()
    {
        return $this->hasMany(VideoQuesAns::class);
    }

    public function watchHistory()
    {
        return $this->hasMany(WatchHistory::class, 'video_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($video) {
            $video->watchHistory()->delete();
        });
    }
}
