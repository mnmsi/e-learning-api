<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Follower extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'followers';

    protected $fillable = [
        'educator_id',
        'learner_id',
    ];

    protected $appends = [
        'is_follow',
    ];

    public function getIsFollowAttribute()
    {
        return $this->learner_id == Auth::id() ? true : false;
    }

    public function educator()
    {
        return $this->belongsTo(User::class, 'educator_id', 'id');
    }
}
