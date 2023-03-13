<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\Course\CourseRepositoryInterface;

class DiscussionRequest extends FormRequest
{
    protected $classRepo;

    protected $courseRepo;

    public function __construct(CourseRepositoryInterface $classRepo, CourseRepositoryInterface $courseRepo)
    {
        parent::__construct();
        $this->classRepo  = $classRepo;
        $this->courseRepo = $courseRepo;
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
            'course_id' => 'required|integer|exists:App\Models\Course,id',
            'parent_id' => 'nullable|integer|exists:App\Models\Discussion,id',
            'comment'   => 'required|string|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->type == 'class') {
                if (!$this->classRepo->whereExists(['id' => $this->type_id])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Invalid class!")
                    );
                }
            } elseif ($this->type == 'course') {
                if (!$this->courseRepo->whereExists(['id' => $this->type_id])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Invalid course!")
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
