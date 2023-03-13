<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    public $timestamps = false;

    protected $fillable = [
        'card_number',
        'card_holder_name',
        'expiry_month',
        'expiry_year',
        'cvc',
        'status',
        'stripe_charge',
        'description',
    ];
}
