<?php

namespace App\Repositories\Discussion;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class DiscussionRepository extends BaseRepository implements DiscussionRepositoryInterface
{
    public function addDiscussion($data)
    {
        $data['user_id'] = Auth::id();

        return $this->model
            ->create($data);
    }
}
