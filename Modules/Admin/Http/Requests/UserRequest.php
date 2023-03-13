<?php

namespace Modules\Admin\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = null;
        $pass   = 'required';
        if (!empty($this->id)) {
            $userId = decrypt($this->id);
            $pass   = 'nullable';
        }

        return [
            'acc_type_id'  => 'required|integer|exists:App\Models\UserAccType,id',
            'age_type_id'  => 'required|integer|exists:App\Models\UserAgeType,id',
            'ethnicity_id' => 'required|integer|exists:App\Models\UserEthnicity,id',
            'name'         => 'required|string|max:190',
            'email'        => 'required|string|email|max:190|unique:App\Models\User,email,' . $userId,
            'phone'        => 'nullable|numeric',
            'birth_date'   => 'nullable|date|date_format:Y-m-d|before_or_equal:' . Carbon::parse(now())->format('Y-m-d'),
            'password'     => 'string|min:8|' . $pass,
            //            'password'     => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()],
            'avatar'       => 'nullable|' . (!is_string($this->avatar) ? 'image|max:5000' : 'string|max:255'),
        ];
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
}
