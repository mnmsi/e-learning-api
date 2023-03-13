<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\InterestedTopic\InterestedTopicRepositoryInterface;

class TopicInterestRequest extends FormRequest
{
    protected $interestTopicRepo;

    public function __construct(InterestedTopicRepositoryInterface $interestTopicRepo)
    {
        parent::__construct();
        $this->interestTopicRepo = $interestTopicRepo;
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
            'topic_id' => 'required|integer|exists:App\Models\Topic,id',
            'interest' => 'required|integer|in:0,1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if ($this->interest) {
                if ($this->interestTopicRepo->whereFirst([
                    'topic_id'   => $this->topic_id,
                    'learner_id' => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Already added!")
                    );
                }
            } else {
                if (!$this->interestTopicRepo->whereFirst([
                    'topic_id'   => $this->topic_id,
                    'learner_id' => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Invalid request!")
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
