<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleAvatar extends Model
{
    use HasFactory;

    protected $table = 'sample_avatars';

    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active',
    ];

    protected $appends = [
        'image_path'
    ];

    public function getImagePathAttribute()
    {
        if (!empty($this->image)) {
            return route('asset', ['data' => $this->image]);
        } else {
            return "";
        }
    }
}
