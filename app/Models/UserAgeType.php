<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAgeType extends Model
{
    use HasFactory;

    protected $table = 'user_age_types';

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];
}
