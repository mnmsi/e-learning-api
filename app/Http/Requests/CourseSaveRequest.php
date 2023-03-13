<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\SaveCourse\SaveCourseRepositoryInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CourseSaveRequest extends FormRequest
{
    protected $saveCourseRepo;

    public function __construct(SaveCourseRepositoryInterface $saveCourseRepo)
    {
        parent::__construct();
        $this->saveCourseRepo = $saveCourseRepo;
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
        return [
            'course_id' => 'required|integer|exists:App\Models\Course,id',
            'save'      => 'required|integer|in:0,1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->save) {
                if ($this->saveCourseRepo->whereFirst([
                    'course_id'  => $this->course_id,
                    'learner_id' => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Already saved!")
                    );
                }
            } else {
                if (!$this->saveCourseRepo->whereFirst([
                    'course_id'  => $this->course_id,
                    'learner_id' => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Already unsaved!")
                    );
                }
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
