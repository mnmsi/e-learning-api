<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'course_id'       => 'required|integer|exists:App\Models\Course,id',
            'title'           => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:1000',
            'location'        => 'nullable|string|max:255',
            'video_thumbnail' => 'nullable|string',
            'duration'        => 'nullable|numeric',
            'order_no'        => 'nullable|integer',
        ];
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
}
