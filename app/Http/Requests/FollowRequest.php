<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use App\Repositories\Follow\FollowRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FollowRequest extends FormRequest
{
    protected $userRepo;
    protected $followRepo;

    public function __construct(UserRepositoryInterface $userRepo, FollowRepositoryInterface $followRepo)
    {
        parent::__construct();
        $this->userRepo   = $userRepo;
        $this->followRepo = $followRepo;
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
            'educator_id' => 'required|integer|exists:App\Models\User,id',
            'follow'      => 'required|integer|in:0,1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($educator = $this->userRepo->findData($this->educator_id)) {
                if ($educator->acc_type->role->id != 1) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Invalid educator!")
                    );
                }
            }

            if ($this->follow) {
                if ($this->followRepo->whereFirst([
                    'educator_id' => $this->educator_id,
                    'learner_id'  => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Already followed!")
                    );
                }
            } else {

                if (!$follow = $this->followRepo->whereFirst([
                    'educator_id' => $this->educator_id,
                    'learner_id'  => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Already unfollowed!")
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
