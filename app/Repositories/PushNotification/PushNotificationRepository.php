<?php

namespace App\Repositories\PushNotification;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class PushNotificationRepository extends BaseRepository implements PushNotificationRepositoryInterface
{
    public function getNotification()
    {
        if (Gate::allows('learner')) {
            return $this->model
                ->where(function ($q) {
                    $q->where('learner_ids', 'LIKE', '[' . Auth::id() . ']')
                      ->orWhere('learner_ids', 'LIKE', '[' . Auth::id() . ',%')
                      ->orWhere('learner_ids', 'LIKE', '%,' . Auth::id() . ',%')
                      ->orWhere('learner_ids', 'LIKE', '%,' . Auth::id() . ']');
                })
                ->orderBy('id', 'DESC')
                ->take(100)
                ->get();
        } else {
            return $this->model
                ->where('tutor_id', Auth::id())
                ->where('action', "project-upload")
                ->orderBy('id', 'DESC')
                ->take(100)
                ->get();
        }
    }
}
