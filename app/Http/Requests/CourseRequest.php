<?php

namespace App\Http\Requests;

use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseConf\CourseConfRepositoryInterface;
use Carbon\Carbon;
use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CourseRequest extends FormRequest
{
    private $courseRepo, $courseConfRepo;

    public function __construct(CourseRepositoryInterface $courseRepo, CourseConfRepositoryInterface $courseConfRepo)
    {
        parent::__construct();
        $this->courseRepo     = $courseRepo;
        $this->courseConfRepo = $courseConfRepo;
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
        if ($this->isMethod('put')) {
            return [
                'course_id' => 'required|integer|exists:App\Models\Course,id',
            ];
        }

        return [
            'privacy'              => 'required|string|in:private,public',
            'subscription_type'    => 'required|string|in:free,paid',
            'amount'               => 'required_if:subscription_type,paid|numeric|min:1',
            'project_instructions' => 'required|string|max:1000',
            'is_for_kid'           => 'nullable|boolean',
            'topic_id'             => 'required|integer|exists:App\Models\Topic,id',
            'name'                 => 'required|string|max:190',
            'type'                 => 'nullable|string|max:255',
            'description'          => 'required|string|max:255',
            'publish_date'         => 'required|date|date_format:Y/m/d|after_or_equal:' . Carbon::parse(now())->format('Y-m-d'),
            'image'                => 'nullable|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function () {
            if (isset($this->image)) {
                if (!Storage::exists($this->image)) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Image not found.")
                    );
                }
            }

            if ($this->courseRepo->whereExists([
                'educator_id' => Auth::id(),
                'name'        => $this->name
            ])) {
                throw new HttpResponseException(
                    Exceptions::validationError("Course already been created by the given name. You need to use another course name.")
                );
            }

            $this->merge([
                'course_fee' => $this->courseConfRepo->getCourseFee()
            ]);
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Exceptions::validationError($validator->errors()->all())
        );
    }
}
