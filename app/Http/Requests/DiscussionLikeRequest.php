<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\DiscussionLike\DiscussionLikeRepositoryInterface;

class DiscussionLikeRequest extends FormRequest
{
    protected $discussionLikeRepo;

    public function __construct(DiscussionLikeRepositoryInterface $discussionLikeRepo)
    {
        parent::__construct();
        $this->discussionLikeRepo = $discussionLikeRepo;
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'discussion_id' => 'required|integer|exists:App\Models\Discussion,id',
            'like'          => 'required|integer|in:0,1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->like) {
                if ($this->discussionLikeRepo->whereFirst([
                    'discussion_id' => $this->discussion_id,
                    'user_id'       => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        response()->json([
                            'status' => false,
                            'msg'    => ["Already liked!"],
                        ])
                    );
                }
            } else {
                if (!$discussion = $this->discussionLikeRepo->whereFirst([
                    'discussion_id' => $this->discussion_id,
                    'user_id'       => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        response()->json([
                            'status' => false,
                            'msg'    => ["Discussion doesn't exists!"],
                        ])
                    );
                }

                if (Auth::user()->cannot('delete', $discussion)) {
                    throw new HttpResponseException(
                        Exceptions::forbidden()
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
