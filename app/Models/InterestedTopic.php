<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestedTopic extends Model
{
    use HasFactory;

    protected $table = 'interested_topics';

    public $timestamps = false;

    protected $fillable = [
        'learner_id',
        'topic_id',
    ];

    protected $with = [
        'topic'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'id');
    }
}
