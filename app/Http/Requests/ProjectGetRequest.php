<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProjectGetRequest extends FormRequest
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
        if (Gate::allows('educator')) {
            return [
                'course_id' => 'required|integer|exists:App\Models\Course,id',
            ];
        } elseif (Gate::allows('learner')) {

            $rules = [];
            // all = any course project
            // onlyMe = only me projectes in course
            // course = public with my projects
            $rules['type'] = 'required|string|in:all,onlyMe,course';
            if ($this->type != 'all') {
                $rules['course_id'] = 'required|integer|exists:App\Models\Course,id';
            }

            return $rules;
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Exceptions::validationError($validator->errors()->all())
        );
    }
}
