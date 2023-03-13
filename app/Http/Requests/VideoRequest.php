<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\Course\CourseRepositoryInterface;

class VideoRequest extends FormRequest
{
    protected $courseRepo;

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
        if ($this->isMethod('put')) {
            return [
                'video_id' => 'required|integer|exists:App\Models\Video,id',
            ];
        }

        return [
            'course_id'       => 'required|integer|exists:App\Models\Course,id',
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string|max:1000',
            'location'        => 'nullable|string|max:255',
            'video_thumbnail' => 'required|string',
            'video_url'       => 'required|string',
            'duration'        => 'required|numeric',
            'order_no'        => 'required|integer',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->isMethod('put')) {
                if (!Storage::exists($this->video_thumbnail)) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Video thumbnail not found.")
                    );
                } elseif (!Storage::exists($this->video_url)) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Video not found.")
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
