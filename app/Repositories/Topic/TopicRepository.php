<?php

namespace App\Repositories\Topic;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class TopicRepository extends BaseRepository implements TopicRepositoryInterface
{
    public function getTopics()
    {
        return Cache::rememberForever('topics', function () {
            return $this->model
                ->where('is_active', 1)
                ->without('category')
                ->get();
        });
    }

    public function getCourseByTopic($filter = null)
    {
        if (!empty($filter['topic_id'])) {
            return $this->model
                ->where('id', $filter['topic_id'])
                ->when(Gate::allows('learner'), function ($query) {
                    $query->whereHas('course', function ($q) {
                        $q->whereStatus(1);
                    });
                })
                ->orderBy('id')
                ->get();
        } else {
            return $this->model
                ->when(Gate::allows('learner'), function ($query) {
                    $query->whereHas('course', function ($q) {
                        $q->whereStatus(1);
                    });
                })
                ->orderBy('id')
                ->get();
        }
    }

    public function getTotalCoursesInEachTopic()
    {
        $topics = $this->model
            ->withCount('total_course')
            ->get();

        $data = array();
        foreach ($topics as $topic) {
            $data[] = [
                'title' => $topic->name,
                'value' => $topic->total_course_count
            ];
        }

        return $data;
    }

    public function getWhatLearnNext()
    {
        return $this->model
            ->with('course')
            ->has('course')
            ->whereHas('course', function ($query) {
                $query->when(Gate::allows('learner'), function ($q) {
                    $q->whereStatus(1);
                });
            })
            ->inRandomOrder()
            ->first();
    }
}
