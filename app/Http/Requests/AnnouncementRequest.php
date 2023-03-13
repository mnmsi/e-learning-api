<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\Course\CourseRepositoryInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AnnouncementRequest extends FormRequest
{
    private $courseRepo;

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
            'course_id'      => 'required|integer|exists:App\Models\Course,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'type'           => 'required|string|in:audio,video',
            'media_path'     => 'required|string',
            'thumbnail_path' => 'nullable|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->courseRepo->whereGetValue(['id' => $this->course_id], 'educator_id') != Auth::id()) {
                throw new HttpResponseException(
                    Exceptions::validationError("You're not owner of selected course.")
                );
            }

            if (!Storage::exists($this->media_path)) {
                throw new HttpResponseException(
                    Exceptions::validationError("Invalid media path.")
                );
            }

            if (isset($this->thumbnail_path)) {
                if (!Storage::exists($this->thumbnail_path)) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Invalid thumbnail path.")
                    );
                }
            } else {
                $this->merge([
                    'thumbnail_path' => "sample_thumbnail/" . $this->type . ".png"
                ]);
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
