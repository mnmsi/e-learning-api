<?php

namespace Modules\Admin\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'privacy'              => 'nullable|string|in:private,public',
            'subscription_type'    => 'nullable|string|in:free,paid',
            'amount'               => 'required_if:subscription_type,paid|numeric|min:' . ($this->subscription_type == "paid" ? 1 : 0),
            'project_instructions' => 'nullable|string|max:1000',
            'is_for_kid'           => 'nullable|boolean',
            'topic_id'             => 'nullable|integer|exists:App\Models\Topic,id',
            'name'                 => 'nullable|string|max:190',
            'type'                 => 'nullable|string|max:255',
            'description'          => 'nullable|string|max:255',
            'publish_date'         => 'nullable|date|date_format:Y-m-d',
            'image'                => 'nullable|string',
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
