<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducatorBalanceWithdraw extends Model
{
    use HasFactory;

    protected $table = 'educator_balance_withdraws';

    protected $fillable = [
        'educator_id',
        'bank_account_id',
        'amount',
        'status',
        'notes'
    ];

    public function educator()
    {
        return $this->belongsTo(User::class, 'educator_id');
    }

    public function bank_account()
    {
        return $this->belongsTo(EducatorBankAccount::class, 'educator_id', 'educator_id');
    }
}
