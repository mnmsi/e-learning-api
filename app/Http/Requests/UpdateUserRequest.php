<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        parent::__construct();
        $this->userRepo = $userRepo;
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
        $validation = [];

        if (isset($this->children)) {
            $validation['children.*.name']      = 'required|string|max:190';
            $validation['children.*.email']     = 'required|string|email|max:190|unique:users,email';
            $validation['children.*.child_age'] = 'required|integer|max:100';
            $validation['children.*.avatar']    = 'nullable';
        }

        if (isset($this->live_class)) {
            $validation['course.name']         = 'required|string|max:190';
            $validation['course.type']         = 'nullable|string|max:255';
            $validation['course.description']  = 'nullable|string|max:255';
            $validation['course.publish_date'] = 'required|date|date_format:Y-m-d|after_or_equal:' . Carbon::parse(now())->format('Y-m-d');
            $validation['course.image']        = 'nullable|mimes:png,tif,tiff,eps,jpeg,jpg|max:255';
        }

        $validation['name']         = 'nullable|string|max:190';
        $validation['avatar']       = 'nullable|' . (!is_string($this->avatar) ? 'image|max:5000' : 'string|max:255');
        $validation['age_type_id']  = 'nullable|integer|exists:App\Models\UserAgeType,id';
        $validation['ethnicity_id'] = 'nullable|integer|exists:App\Models\UserEthnicity,id';

        return $validation;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!empty($this->acc_type_id) && !empty(Auth::user()->social_uid) && Auth::user()->is_acc_type_update == 1) {
                throw new HttpResponseException(
                    Exceptions::validationError("Unable to change account type!")
                );
            } elseif (!empty($this->password)) {
                throw new HttpResponseException(
                    Exceptions::validationError("Please reset your password if you want to update it!")
                );
            } elseif (count($this->all()) == 0) {
                throw new HttpResponseException(
                    Exceptions::validationError("Empty request!")
                );
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
