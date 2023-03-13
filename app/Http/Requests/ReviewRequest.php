<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\Review\ReviewRepositoryInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewRequest extends FormRequest
{
    protected $reviewRepo;

    public function __construct(ReviewRepositoryInterface $reviewRepo)
    {
        parent::__construct();
        $this->reviewRepo = $reviewRepo;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Gate::allows('educator')) {
            throw new HttpResponseException(
                Exceptions::validationError("You can't review your own course")
            );
        }

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
            'course_id'   => 'required|integer|exists:App\Models\Course,id',
            'rate'        => 'required|integer|in:1,2,3,4,5',
            'review_text' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->reviewRepo->whereExists([
                'learner_id' => Auth::id(),
                'course_id'  => $this->course_id,
            ])) {
                throw new HttpResponseException(
                    Exceptions::validationError("Already reviewed by current user!")
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
