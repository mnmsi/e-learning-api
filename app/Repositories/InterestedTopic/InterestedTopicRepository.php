<?php

namespace App\Repositories\InterestedTopic;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class InterestedTopicRepository extends BaseRepository implements InterestedTopicRepositoryInterface
{
    public function addNewInterest($data)
    {
        if ($data['interest'] == 1) {
            return $this->model->create([
                'topic_id'   => $data['topic_id'],
                'learner_id' => Auth::id(),
            ]);
        } else {
            return $this->model
                ->where('topic_id', $data['topic_id'])
                ->where('learner_id', Auth::id())
                ->delete();
        }
    }
}
