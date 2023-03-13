<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'learner_ids',
        'action',
        'tutor_id',
        'tutor_name',
        'data',
        'is_seen'
    ];

    protected $hidden = [
        'learner_ids'
    ];

    protected $casts = [
        'learner_ids' => 'array',
        'data'        => 'array',
    ];

    public function getDataAttribute($value)
    {
        $data = json_decode($value);
        if (!empty($data->image)) {
            $data->image = route('asset', ['data' => $data->image]);
        }

        return $data;
    }
}
