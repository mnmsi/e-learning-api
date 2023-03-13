<?php

namespace App\Repositories\DiscussionLike;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class DiscussionLikeRepository extends BaseRepository implements DiscussionLikeRepositoryInterface
{
    public function likeDiscussion($data)
    {
        if ($data['like'] == 1) {
            return $this->model->create([
                'user_id'       => Auth::id(),
                'discussion_id' => $data['discussion_id'],
            ]);
        } else {

            $like = $this->model
                ->where([
                    'user_id'       => Auth::id(),
                    'discussion_id' => $data['discussion_id'],
                ])->first();

            return $like->delete();
        }
    }
}
