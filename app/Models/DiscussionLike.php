<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscussionLike extends Model
{
    use HasFactory;

    protected $table = 'discussion_likes';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'discussion_id'
    ];

    protected $appends = [
        'user_like'
    ];

    public function getUserLikeAttribute()
    {
        return $this->user_id == Auth::id() ? true : false;
    }
}
