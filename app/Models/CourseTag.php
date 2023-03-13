<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTag extends Model
{
    use HasFactory;

    protected $table = 'course_tags';

    protected $fillable = [
        'title',
        'value',
        'description'
    ];
}
