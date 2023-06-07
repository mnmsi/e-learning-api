<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'image',
        'description',
        'is_active',
    ];

    public function course()
    {
        return $this->hasMany(Course::class)->where(function ($q) {
            if (Gate::allows('educator')) {
                $q->where('educator_id', Auth::id());
            }
        })->take(5);
    }

    public function getImageAttribute($value)
    {
        if($value)
        {
            return route('asset', ['data' => $value]);
        }
        return $value;
    }
}
