<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\Video\VideoRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\Course\CourseRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WatchHistoryRequest extends FormRequest
{
    protected $courseRepo, $videoRepo;

    public function __construct(CourseRepositoryInterface $courseRepo, VideoRepositoryInterface $videoRepo)
    {
        parent::__construct();
        $this->courseRepo = $courseRepo;
        $this->videoRepo  = $videoRepo;
    }

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
            'course_id'   => 'required|integer|exists:App\Models\Course,id',
            'video_id'    => 'required|integer|exists:App\Models\Video,id',
            'duration'    => 'required|numeric',
            'is_complete' => 'nullable|boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function () {
            $course = $this->courseRepo->courseDetails($this->course_id);
            if ($this->user()->cannot('isEnrolled', $course)) {
                throw new HttpResponseException(
                    Exceptions::validationError("Invalid user.")
                );
            }

            if ($video = $this->videoRepo->findData($this->video_id)) {
                if ($video->duration == $this->duration) {
                    if (!isset($this->is_complete)) {
                        $this->merge(['is_complete' => 1]);
                    }
                } elseif ($video->duration > $this->duration) {
                    if (isset($this->is_complete) && $this->is_complete == 1) {
                        unset($this['is_complete']);
                    }
                } elseif ($video->duration < $this->duration) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Duration exceed actual video duration.")
                    );
                }
            } else {
                throw new HttpResponseException(
                    Exceptions::validationError("Video Doesn't exists.")
                );
            }


            $this->merge(['learner_id' => Auth::id()]);
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Exceptions::validationError($validator->errors()->all())
        );
    }
}
