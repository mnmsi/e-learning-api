<?php

namespace App\Repositories\SaveCourse;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class SaveCourseRepository extends BaseRepository implements SaveCourseRepositoryInterface
{
    public function saveCourse($data)
    {
        if ($data['save'] == 1) {
            return $this->model->create([
                'course_id'  => $data['course_id'],
                'learner_id' => Auth::id(),
            ]);
        } else {
            return $this->model
                ->where('course_id', $data['course_id'])
                ->where('learner_id', Auth::id())
                ->delete();
        }
    }

    public function saveCourseFromJob($data)
    {
        if (!$this->model
            ->where('course_id', $data['course_id'])
            ->where('learner_id', $data['learner_id'])
            ->exists()
        ) {
            return $this->model->create([
                'course_id'  => $data['course_id'],
                'learner_id' => $data['learner_id'],
            ]);
        }

        return false;
    }

    public function getLearnerSavedCourse()
    {
        return $this->model
            ->where('learner_id', Auth::id())
            ->with(['course' => function ($q) {
                $q->withoutGlobalScope('privacy')
                  ->withoutGlobalScope('publish_date');
            }])
            ->get();
    }
}
