<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\Educator\EducatorRepositoryInterface;
use App\Repositories\EducatorBankAccount\EducatorBankAccountRepositoryInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WithdrawRequest extends FormRequest
{
    public $educatorRepo, $bankAccRepo;

    /**
     * @param EducatorRepositoryInterface $educatorRepo
     * @param EducatorBankAccountRepositoryInterface $bankAccRepo
     */
    public function __construct(EducatorRepositoryInterface $educatorRepo, EducatorBankAccountRepositoryInterface $bankAccRepo)
    {
        parent::__construct();
        $this->educatorRepo = $educatorRepo;
        $this->bankAccRepo  = $bankAccRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('educator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $balanceInfo = (object)$this->educatorRepo->getBalanceInfo();
        $bankAccInfo = $this->bankAccRepo->findData($this->bank_account_id);

        if (!$bankAccInfo) {
            throw new HttpResponseException(
                Exceptions::validationError("Invalid bank account id.")
            );
        }

        if ($bankAccInfo->educator_id !== Auth::id()) {
            throw new HttpResponseException(
                Exceptions::validationError("Invalid bank account id.")
            );
        }

        if ($this->amount > $balanceInfo->available_balance) {
            throw new HttpResponseException(
                Exceptions::validationError("Insufficient balance.")
            );
        }

        $this->merge(['educator_id' => Auth::id()]);

        return [
            'bank_account_id' => 'required|integer|exists:App\Models\EducatorBankAccount,id',
            'amount'          => 'required|numeric|min:5'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Exceptions::validationError($validator->errors()->all())
        );
    }
}
