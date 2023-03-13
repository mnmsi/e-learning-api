<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccType extends Model
{
    use HasFactory;

    protected $table = 'user_acc_types';

    protected $fillable = [
        'role_id',
        'name',
        'description',
        'is_active',
    ];

    protected $with = [
        'role'
    ];

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id', 'id');
    }
}
