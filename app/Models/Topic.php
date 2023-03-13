<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Topic extends Model
{
    use HasFactory;

    protected $table = 'topics';

    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active'
    ];

    public function getImageAttribute($value)
    {
        if (!empty($value)) {
            return route('asset', ['data' => $value]);
        } else {
            return "";
        }
    }

    public function course()
    {
        return $this->hasMany(Course::class)->where(function ($q) {
            if (Gate::allows('educator')) {
                $q->where('educator_id', Auth::id());
            }
        })->take(8);
    }

    public function total_course()
    {
        return $this->hasMany(Course::class);
    }
}
