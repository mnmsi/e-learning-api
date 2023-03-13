<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducatorBankAccount extends Model
{
    use HasFactory;

    protected $table = 'educator_bank_accounts';

    protected $fillable = [
        'educator_id',
        'account_name',
        'account_no'
    ];
}
