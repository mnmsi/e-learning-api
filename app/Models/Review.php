<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    public $timestamps = false;

    protected $fillable = [
        'learner_id',
        'course_id',
        'rate',
        'review_text',
    ];

    protected $with = [
        'learner',
    ];

    public function learner()
    {
        return $this->belongsTo(User::class, 'learner_id');
    }
}
