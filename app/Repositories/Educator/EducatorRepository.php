<?php

namespace App\Repositories\Educator;

use App\Http\Resources\RecentTransactionsCollection;
use App\Http\Resources\StudentInfoResource;
use App\Repositories\BaseRepository;

class EducatorRepository extends BaseRepository implements EducatorRepositoryInterface
{
    protected $model, $courseRepo, $courseEnrollRepo, $balanceWithdrawRepo;

    public function __construct($model, $courseRepo, $courseEnrollRepo, $balanceWithdrawRepo)
    {
        parent::__construct($model);
        $this->courseRepo          = $courseRepo;
        $this->courseEnrollRepo    = $courseEnrollRepo;
        $this->balanceWithdrawRepo = $balanceWithdrawRepo;
    }

    public function getStudents()
    {
        return $this->courseEnrollRepo->getStudents();
    }

    public function getBalanceInfo()
    {
        $totalEnrolled = $this->courseEnrollRepo->getEnrolledCourses();
        $coursesAmount = collect($totalEnrolled)->map(function ($item) {
            return doubleval($this->courseRepo->applyCourseCharge($item->course->course_fee, $item->course->amount));
        });

        $actualEarn       = $coursesAmount->sum();
        $pendingBalance   = $this->balanceWithdrawRepo->getPendingBalance();
        $approvedBalance  = $this->balanceWithdrawRepo->getApprovedWithdrawAmount();
        $availableBalance = $actualEarn - $pendingBalance - $approvedBalance;

        return [
            'total_earn'        => $actualEarn,
            'available_balance' => $availableBalance,
            'pending_balance'   => $pendingBalance
        ];
    }

    public function recentTransactions()
    {
        return $this->courseEnrollRepo->getRecentTransaction();
    }

    public function getMyWithdraws()
    {
        return $this->balanceWithdrawRepo->getMyWithdraws();
    }

    public function createWithdrawRequest($data)
    {
        return $this->balanceWithdrawRepo->insertData($data);
    }
}
