<?php

namespace App\Repositories\CourseEnroll;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CourseEnrollRepository extends BaseRepository implements CourseEnrollRepositoryInterface
{
    public function getStudents($id = null)
    {
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();

        $data = $this->model
            ->select('learner_id', 'course_id', 'created_at')
            ->whereHas('course', function ($q) use ($id) {
                $q->withoutGlobalScope('privacy')
                  ->withoutGlobalScope('publish_date');

                if (Gate::allows('educator')) {
                    $q->where('educator_id', Auth::id());
                } elseif (Gate::allows('learner')) {
                    $q->where('educator_id', $id);
                }
            })
            ->groupBy('learner_id')
            ->get();

        config()->set('database.connections.mysql.strict', true);
        DB::reconnect();

        return $data;
    }

    public function getLearnerEnrolledCourse()
    {
        return $this->model
            ->without('learner')
            ->with(['course' => function ($q) {
                $q->withoutGlobalScope('privacy')->withoutGlobalScope('publish_date');
            }])
            ->where('learner_id', Auth::id())
            ->get();
    }

    public function getEnrolled($subscriptionType = null)
    {
        return $this->model
            ->with('course')
            ->without('learner')
            ->whereHas('course', function ($q) use ($subscriptionType) {
                $q->when($subscriptionType, function ($qs, $subscriptionType) {
                    $qs->where('subscription_type', $subscriptionType);
                });
            })
            ->get();
    }

    public function checkStudentEnroll($learnerId)
    {
        return $this->model
            ->where('educator_id', Auth::id())
            ->where('learner_id', $learnerId)
            ->exists();
    }

    public function getEnrolledCourses()
    {
        return $this->model
            ->with('course')
            ->without('learner')
            ->whereHas('course', function ($q) {
                $q->where('educator_id', Auth::id())
                  ->where('subscription_type', 'paid');
            })
            ->whereHas('subscription', function ($q) {
                $q->where('status', 1);
            })
            ->get();
    }

    public function getRecentTransaction()
    {
        return $this->model
            ->with('course', 'subscription')
            ->whereHas('course', function ($q) {
                $q->where('educator_id', Auth::id())
                  ->where('subscription_type', 'paid');
            })
            ->whereHas('subscription', function ($q) {
                $q->where('status', 1);
            })
            ->orderBy('id', 'desc')
            ->paginate(15);
    }

    public function getEnrolledCoursesOfLearner($learnerId, array $educatorCourses)
    {
        return $this->model
            ->without('learner')
            ->where('learner_id', $learnerId)
            ->whereIn('course_id', $educatorCourses)
            ->with(['course' => function ($q) {
                $q->where('educator_id', Auth::id())
                  ->withoutGlobalScope('privacy')
                  ->withoutGlobalScope('publish_date');
            }, 'subscription'])
            ->get();
    }
}
