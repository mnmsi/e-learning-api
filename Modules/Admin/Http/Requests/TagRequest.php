<?php

namespace Modules\Admin\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TagRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->merge([
            'title' => Str::lower(Str::replace(' ', '_', $this->title))
        ]);

        return [
            'title'       => 'required|string|unique:App\Models\CourseTag,title',
            'value'       => 'required|numeric',
            'description' => 'nullable|string',
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
