<?php

namespace App\Http\Requests;

use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\ProjectLike\ProjectLikeRepositoryInterface;

class ProjectLikeRequest extends FormRequest
{
    protected $projectLikeRepo;

    public function __construct(ProjectLikeRepositoryInterface $projectLikeRepo)
    {
        parent::__construct();
        $this->projectLikeRepo = $projectLikeRepo;
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
            'project_id' => 'required|integer|exists:App\Models\Project,id',
            'like'       => 'required|integer|in:0,1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->like) {
                if ($this->projectLikeRepo->whereFirst([
                    'project_id' => $this->project_id,
                    'learner_id' => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Already liked!")
                    );
                }
            } else {

                if (!$project = $this->projectLikeRepo->whereFirst([
                    'project_id' => $this->project_id,
                    'learner_id' => Auth::id(),
                ])) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Project doesn't exists!")
                    );
                }

                if (Auth::user()->cannot('delete', $project)) {
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
