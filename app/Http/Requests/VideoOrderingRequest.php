<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\Video\VideoRepositoryInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class VideoOrderingRequest extends FormRequest
{
    protected $videoRepo;

    public function __construct(VideoRepositoryInterface $videoRepo)
    {
        parent::__construct();
        $this->videoRepo = $videoRepo;
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
            'course_id'         => 'required|integer|exists:App\Models\Course,id',
            'videos.*.video_id' => 'required|integer|distinct:strict|exists:App\Models\Video,id',
            'videos.*.position' => 'required|integer|distinct:strict|lte:' . $this->videoRepo->whereCount([
                'educator_id' => Auth::id(),
                'course_id'   => $this->course_id,
            ]),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Exceptions::validationError($validator->errors()->all())
        );
    }
}
