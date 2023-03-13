<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Discussion extends Model
{
    use HasFactory;

    protected $table = 'discussions';

    protected $fillable = [
        'user_id',
        'course_id',
        'parent_id',
        'comment',
    ];

    protected $with = [
        'user', 'sub_comment', 'user_like',
    ];

    protected $withCount = [
        'total_like',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function sub_comment()
    {
        return $this->hasMany(Discussion::class, 'parent_id', 'id');
    }

    public function total_like()
    {
        return $this->hasMany(DiscussionLike::class, 'discussion_id', 'id');
    }

    public function user_like()
    {
        return $this->hasOne(DiscussionLike::class, 'discussion_id', 'id')->whereUserId(Auth::id());
    }
}
