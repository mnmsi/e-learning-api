<?php

namespace App\Http\Requests;

use App\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\Exceptions;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class PasswordResetRequest extends FormRequest
{
    protected $userRepo, $passwordResetRepo;

    public function __construct(UserRepositoryInterface $userRepo, PasswordResetRepositoryInterface $passwordResetRepo)
    {
        parent::__construct();
        $this->userRepo          = $userRepo;
        $this->passwordResetRepo = $passwordResetRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token'            => 'required|exists:App\Models\PasswordReset,token',
            'password'         => 'required|min:8',
            'confirm_password' => 'required_with:password|same:password|min:8'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function () {
            $passReset = $this->passwordResetRepo->whereFirst(['token' => $this->token]);
            if (!$passReset) {
                throw new HttpResponseException(
                    Exceptions::validationError("Invalid token!")
                );
            }

            $current   = Carbon::now();
            $createdAt = Carbon::parse($passReset->created_at);
            $educator  = $this->userRepo->whereFirst(['email' => $passReset->email]);

            if ($createdAt->diffInMinutes($current) >= 5) {
                throw new HttpResponseException(
                    Exceptions::validationError("Token is expired!")
                );
            } elseif (!$educator) {
                throw new HttpResponseException(
                    Exceptions::validationError("Invalid token!")
                );
            } elseif ($educator->email != $passReset->email) {
                throw new HttpResponseException(
                    Exceptions::validationError("Invalid token!")
                );
            } elseif ($educator->id != decrypt($this->token)) {
                throw new HttpResponseException(
                    Exceptions::validationError("Invalid token!")
                );
            } else {
                $this->merge([
                    "user_id"  => $educator->id,
                    'password' => Hash::make($this->password)
                ]);
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Exceptions::validationError($validator->errors()->all())
        );
    }
}
