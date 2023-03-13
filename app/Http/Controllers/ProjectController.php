<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Requests\ProjectGetRequest;
use App\Http\Requests\ProjectLikeRequest;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Repositories\ProjectLike\ProjectLikeRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    protected $projectRepo;

    protected $projectLikeRepo;

    public function __construct(
        ProjectRepositoryInterface $projectRepo,
        ProjectLikeRepositoryInterface $projectLikeRepo
    ) {
        $this->projectRepo     = $projectRepo;
        $this->projectLikeRepo = $projectLikeRepo;
    }

    public function projects(ProjectGetRequest $request)
    {
        try {
            if ($data = $this->projectRepo->getProjects($request->all())) {
                return response()->json([
                    'status' => true,
                    'data'   => ProjectResource::collection($data),
                ]);
            } else {
                return Exceptions::error();
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function createProject(ProjectRequest $request)
    {
        try {
            $request['learner_id'] = Auth::id();
            if ($data = $this->projectRepo->insertData($request->all())) {
                return response()->json([
                    'status' => true,
                    'data'   => $data,
                ]);
            } else {
                return Exceptions::error();
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function likeProject(ProjectLikeRequest $request)
    {
        try {
            if ($this->projectLikeRepo->likeProject($request->all())) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function deleteProject(Project $project)
    {
        try {
            if (Auth::user()->cannot('delete', $project)) {
                return Exceptions::forbidden();
            }

            if ($project->delete()) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }

        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

}
