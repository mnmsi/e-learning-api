<?php

namespace App\Repositories\Follow;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class FollowRepository extends BaseRepository implements FollowRepositoryInterface
{
    public function follow($data)
    {
        if ($data['follow'] == 1) {
            return $this->model->create([
                'educator_id' => $data['educator_id'],
                'learner_id'  => Auth::id(),
            ]);
        } else {
            return $this->model
                ->where('educator_id', $data['educator_id'])
                ->where('learner_id', Auth::id())
                ->delete();
        }
    }
}
