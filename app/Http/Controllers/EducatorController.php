<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\EducatorStudentList;
use App\Http\Resources\EducatorWithdrawCollection;
use App\Http\Resources\RecentTransactionsCollection;
use App\Repositories\Educator\EducatorRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class EducatorController extends Controller
{
    protected $educatorRepo;

    public function __construct(EducatorRepositoryInterface $educatorRepo)
    {
        $this->educatorRepo = $educatorRepo;
    }

    public function studentList()
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => EducatorStudentList::collection($this->educatorRepo->getStudents()),
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function balanceInfo()
    {
        try {
            return [
                'status' => true,
                'data'   => $this->educatorRepo->getBalanceInfo()
            ];
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function myWithdraws()
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => new EducatorWithdrawCollection($this->educatorRepo->getMyWithdraws()),
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function transactions()
    {
        try {
            return response()->json([
                'status'       => true,
                'transactions' => new RecentTransactionsCollection($this->educatorRepo->recentTransactions()),
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function withdrawRequest(WithdrawRequest $request)
    {
        try {
            if ($isCreate = $this->educatorRepo->createWithdrawRequest($request->all())) {
                return [
                    'status' => true,
                    'data'   => $isCreate
                ];
            }

            return Exceptions::error("Something went wrong!");
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }
}
