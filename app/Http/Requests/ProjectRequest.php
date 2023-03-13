<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProjectRequest extends FormRequest
{
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
            'course_id'  => 'required|integer|exists:App\Models\Course,id',
            'caption'    => 'nullable|string|max:255',
            'type'       => 'required|string|in:image,video',
            'media'      => 'required|string',
            'thumbnail'  => 'required_if:type,video|string',
            'is_private' => 'nullable|in:0,1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!Storage::exists($this->media)) {
                throw new HttpResponseException(
                    Exceptions::validationError("Invalid media path.")
                );
            }

            if (!Storage::exists($this->thumbnail) && $this->type == 'video') {
                throw new HttpResponseException(
                    Exceptions::validationError("Invalid thumbnail path.")
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
