<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\EducatorBankAccount\EducatorBankAccountRepositoryInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class EducatorAccountRequest extends FormRequest
{
    private $bankAccRepo;

    /**
     * @param EducatorBankAccountRepositoryInterface $bankAccRepo
     */
    public function __construct(EducatorBankAccountRepositoryInterface $bankAccRepo)
    {
        parent::__construct();
        $this->bankAccRepo = $bankAccRepo;
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
        if (Str::length($this->account_no) > 30) {
            throw new HttpResponseException(
                Exceptions::validationError("Account no can't be more than 30 character")
            );
        }

        $this->merge(['educator_id' => Auth::id()]);

        if ($this->isMethod('PUT')) {
            if ($isExists = $this->bankAccRepo->whereFirst(['account_no' => $this->account_no, 'educator_id' => Auth::id()])) {
                $accountInfo = $this->bankAccRepo->findData($this->id);
                if (!$accountInfo || $accountInfo->educator_id != Auth::id()) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Invalid id.")
                    );
                }

                if ($isExists->id != $this->id) {
                    throw new HttpResponseException(
                        Exceptions::validationError("The account no has already been taken.")
                    );
                }
            }

            return [
                'account_name' => 'required|string|max:100',
                'account_no'   => 'required|numeric|digits_between:15,18'
            ];
        }

        return [
            'account_name' => 'required|string|max:100',
            'account_no'   => 'required|numeric|digits_between:15,18|unique:App\Models\EducatorBankAccount,account_no,' . $this->account_no . ',id,educator_id,' . Auth::id()
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Exceptions::validationError($validator->errors()->all())
        );
    }
}
