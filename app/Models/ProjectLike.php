<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectLike extends Model
{
    use HasFactory;

    protected $table = 'project_likes';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'learner_id',
    ];

    protected $appends = [
        'is_like',
    ];

    public function getIsLikeAttribute()
    {
        return $this->learner_id == Auth::id() ? true : false;
    }
}
