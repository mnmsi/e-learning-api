<?php

namespace App\Repositories\EducatorBalanceWithdraw;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class EducatorBalanceWithdrawRepository extends BaseRepository implements EducatorBalanceWithdrawRepositoryInterface
{
    public function getPendingBalance()
    {
        return (double)$this->model
            ->where('educator_id', Auth::id())
            ->where('status', 0)
            ->sum('amount');
    }

    public function getApprovedWithdrawAmount()
    {
        return (double)$this->model
            ->where('educator_id', Auth::id())
            ->where('status', 1)
            ->sum('amount');
    }

    public function getRejectedWithdrawAmount()
    {
        return (double)$this->model
            ->where('educator_id', Auth::id())
            ->where('status', 2)
            ->sum('amount');
    }

    public function getMyWithdraws()
    {
        return $this->model
            ->where('educator_id', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(15);
    }
}
