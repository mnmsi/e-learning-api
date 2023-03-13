<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Repositories\EducatorBalanceWithdraw\EducatorBalanceWithdrawRepositoryInterface;
use App\Repositories\EducatorBankAccount\EducatorBankAccountRepositoryInterface;
use App\Repositories\Topic\TopicRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Notifications\WithdrawApproveNotification;
use Modules\Admin\Notifications\WithdrawRejectNotification;

class TransactionController extends Controller
{
    private $withdrawRepo;

    public function __construct(EducatorBalanceWithdrawRepositoryInterface $withdrawRepo)
    {
        $this->withdrawRepo = $withdrawRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function transactions()
    {
        $withdraws = $this->withdrawRepo->getData(15, "DESC");
        $withdraws->load('educator', 'bank_account');

        return view('admin::pages.transaction.list', [
            'withdraws' => $withdraws
        ]);
    }

    public function approve($id)
    {
        try {
            if ($withdraw = $this->withdrawRepo->findData(decrypt($id))) {
                if ($withdraw->status != 1) {
                    if ($withdraw->update(['status' => 1])) {
                        $withdraw->educator->notify(new WithdrawApproveNotification());
                        return Exceptions::success("Successfully approved!");
                    }

                    return Exceptions::error("Something went wrong!");
                }

                return Exceptions::error("Transaction already approved!");
            }

            return Exceptions::error("Invalid Id");
        }
        catch (\Exception $exception) {
            return Exceptions::error();
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            if ($withdraw = $this->withdrawRepo->findData(decrypt($id))) {
                if ($withdraw->status != 2) {
                    if ($withdraw->update(['status' => 2, 'notes' => $request->notes])) {
                        $withdraw->educator->notify(new WithdrawRejectNotification($request->notes));
                        return Exceptions::success("Successfully rejected!");
                    }

                    return Exceptions::error("Something went wrong!");
                }

                return Exceptions::error("Transaction already rejected!");
            }

            return Exceptions::error("Invalid Id");
        }
        catch (\Exception $exception) {
            return Exceptions::error();
        }
    }
}
