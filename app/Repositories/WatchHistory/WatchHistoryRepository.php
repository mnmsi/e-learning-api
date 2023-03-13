<?php

namespace App\Repositories\WatchHistory;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WatchHistoryRepository extends BaseRepository implements WatchHistoryRepositoryInterface
{
    public function getWatchedCourses()
    {
        return $this->model
            ->where('learner_id', Auth::id())
            ->where('is_complete', 1)
            ->distinct('course_id')
            ->pluck('course_id')
            ->all();
    }

    public function getContinueWatchingCourses()
    {
        return $this->model
            ->where('learner_id', Auth::id())
            ->distinct('course_id')
            ->pluck('course_id')
            ->all();
    }

    public function checkVideoForWatched($videos)
    {
        foreach ($videos as $video) {
            $isComplete = $this->model
                ->where('video_id', $video)
                ->where('is_complete', 1)
                ->exists();

            if (!$isComplete) {
                return false;
            }
        }

        return true;
    }

    public function checkVideoForContinueWatch($videos)
    {
        if ($this->checkVideoForWatched($videos)) {
            return false;
        }

        $totalWatchVideos = $this->model
            ->whereIn('video_id', $videos)
            ->get();

        if ($totalWatchVideos->count() == 0) {
            return false;
        }

        if (count($totalWatchVideos->where('is_complete', 0)) > 0) {
            return true;
        }

        if (count($videos) != count($totalWatchVideos->where('is_complete', 1))) {
            return true;
        }

        return false;
    }
}
