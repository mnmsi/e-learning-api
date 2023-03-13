<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
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
            'acc_type_id'  => 'required|integer|exists:App\Models\UserAccType,id',
            'age_type_id'  => 'nullable|integer|exists:App\Models\UserAgeType,id',
            'ethnicity_id' => 'nullable|integer|exists:App\Models\UserEthnicity,id',
            'name'         => 'required|string|max:190',
            'email'        => 'required|string|email|max:190|unique:users,email',
            'password'     => 'required|string|min:8',
            //            'password'     => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()],
            'avatar'       => 'nullable|' . (!is_string($this->avatar) ? 'image|max:5000' : 'string|max:255'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Exceptions::validationError($validator->errors()->all())
        );
    }
}
