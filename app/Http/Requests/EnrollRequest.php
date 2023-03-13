<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;

class EnrollRequest extends FormRequest
{
    protected $courseRepo, $enrollRepo;

    public function __construct(
        CourseRepositoryInterface       $courseRepo,
        CourseEnrollRepositoryInterface $enrollRepo
    )
    {
        parent::__construct();
        $this->courseRepo = $courseRepo;
        $this->enrollRepo = $enrollRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('learner');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'course_id' => 'required|integer|exists:App\Models\Course,id',
        ];

        if ($this->courseRepo->whereGetValue(['id' => $this->course_id], 'subscription_type') == 'paid') {
            $rules['card_number']      = 'required|numeric';
            $rules['card_holder_name'] = 'required|string|max:50';
            $rules['expiry_month']     = 'required|date_format:m';
            $rules['expiry_year']      = 'required|date_format:Y';
            $rules['cvc']              = 'required|numeric';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->enrollRepo->whereExists([
                'course_id'  => $this->course_id,
                'learner_id' => Auth::id(),
            ])) {
                throw new HttpResponseException(
                    Exceptions::validationError("Already enrolled!")
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
