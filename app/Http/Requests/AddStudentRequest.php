<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\Course\CourseRepositoryInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class AddStudentRequest extends FormRequest
{
    private $courseRepo;

    /**
     * @param CourseRepositoryInterface $courseRepo
     */
    public function __construct(CourseRepositoryInterface $courseRepo)
    {
        parent::__construct();
        $this->courseRepo = $courseRepo;
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
        return [
            'course_id' => 'required|exists:App\Models\Course,id',
            'file'      => 'required|mimes:csv,xls,xlsx',
        ];
    }

    protected function passedValidation()
    {
        if (($course = $this->courseRepo->validateForAddStudentInCourse($this->course_id)) !== true) {
            throw new HttpResponseException(
                Exceptions::validationError($course)
            );
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Exceptions::validationError($validator->errors()->all())
        );
    }
}
