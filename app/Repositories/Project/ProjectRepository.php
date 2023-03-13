<?php

namespace App\Repositories\Project;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    protected $videoRepo;

    public function __construct($model, $videoRepo)
    {
        parent::__construct($model);
        $this->videoRepo = $videoRepo;
    }

    public function getProjects($filter)
    {
        return $this->model
            ->where(function ($q) use ($filter) {
                if (Gate::allows('educator')) {
                    return $q->where('course_id', $filter['course_id']);

                } elseif (Gate::allows('learner')) {

                    if ($filter['type'] == 'onlyMe') {

                        return $q->where('course_id', $filter['course_id'])
                            ->where('learner_id', Auth::id());
                    } elseif ($filter['type'] == 'course') {

                        return $q->where('course_id', $filter['course_id'])
                            ->where('is_private', 0);
                    } else {
                        return $q->where('learner_id', Auth::id());
                    }
                }
            })
            ->get();
    }
}
