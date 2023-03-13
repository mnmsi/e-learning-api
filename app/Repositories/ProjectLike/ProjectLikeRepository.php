<?php

namespace App\Repositories\ProjectLike;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class ProjectLikeRepository extends BaseRepository implements ProjectLikeRepositoryInterface
{
    public function likeProject($data)
    {
        if ($data['like'] == 1) {
            return $this->model->create([
                'learner_id' => Auth::id(),
                'project_id' => $data['project_id'],
            ]);
        } else {

            $like = $this->model
                ->where([
                    'learner_id' => Auth::id(),
                    'project_id' => $data['project_id'],
                ])->first();
                
            return $like->delete();
        }
    }
}
