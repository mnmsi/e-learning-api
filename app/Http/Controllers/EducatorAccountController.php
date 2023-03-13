<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Requests\EducatorAccountRequest;
use App\Repositories\EducatorBankAccount\EducatorBankAccountRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducatorAccountController extends Controller
{
    private $bankAccRepo;

    /**
     * @param EducatorBankAccountRepositoryInterface $bankAccRepo
     */
    public function __construct(EducatorBankAccountRepositoryInterface $bankAccRepo)
    {
        $this->bankAccRepo = $bankAccRepo;
    }

    public function list()
    {
        try {
            if ($data = $this->bankAccRepo->whereGet(['educator_id' => Auth::id()])) {
                return [
                    'status' => true,
                    'data'   => $data
                ];
            }

            return Exceptions::error("No data found!", 404);
        }
        catch (\Exception $exception) {
            return Exceptions::error();
        }
    }

    public function addAccount(EducatorAccountRequest $request)
    {
        try {
            if ($data = $this->bankAccRepo->insertData($request->all())) {
                return [
                    'status' => true,
                    'data'   => $data
                ];
            }

            return Exceptions::error("Something went wrong!");
        }
        catch (\Exception $exception) {
            return Exceptions::error();
        }
    }

    public function accountDetails($id)
    {
        try {
            $data = $this->bankAccRepo->findData($id);

            if (!$data || $data->educator_id !== Auth::id()) {
                return Exceptions::error("Invalid Id!", 404);
            }

            return [
                'status' => true,
                'data'   => $data
            ];
        }
        catch (\Exception $exception) {
            return Exceptions::error();
        }
    }

    public function updateAccount(EducatorAccountRequest $request)
    {
        try {
            $data = $this->bankAccRepo->findData($request->id);

            if (!$data || $data->educator_id !== Auth::id()) {
                return Exceptions::error("Invalid Id!", 404);
            }

            if ($data->update($request->all())) {
                return Exceptions::success("Successfully updated!");
            }

            return Exceptions::error("Something went wrong!");
        }
        catch (\Exception $exception) {
            return Exceptions::error();
        }
    }

    public function deleteAccount($id)
    {
        try {
            $data = $this->bankAccRepo->findData($id);

            if (!$data || $data->educator_id != Auth::id()) {
                return Exceptions::error("Invalid Id!", 404);
            }

            if ($data->delete()) {
                return Exceptions::success();
            }

            return Exceptions::error();
        }
        catch (\Exception $exception) {
            if ($exception instanceof QueryException) {
                return Exceptions::error("Unable to delete account. Some of transactions are involved with this account.");
            }
            return Exceptions::error();
        }
    }
}
