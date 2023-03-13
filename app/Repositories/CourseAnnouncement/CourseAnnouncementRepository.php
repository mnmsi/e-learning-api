<?php

namespace App\Repositories\CourseAnnouncement;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CourseAnnouncementRepository extends BaseRepository implements CourseAnnouncementRepositoryInterface
{
    public function getAll()
    {
        return $this->model
            ->whereHas('course', function ($q) {
                $q->where('educator_id', Auth::id());
            })
            ->get();
    }
}
