<?php

namespace App\Repositories\Course;

use App\Exceptions\Exceptions;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    protected $enrollRepo, $subscriptionRepo, $courseTagRepo, $courseConfRepo;

    public function __construct($model, $enrollRepo, $subscriptionRepo, $courseTagRepo, $courseConfRepo)
    {
        parent::__construct($model);
        $this->enrollRepo       = $enrollRepo;
        $this->subscriptionRepo = $subscriptionRepo;
        $this->courseTagRepo    = $courseTagRepo;
        $this->courseConfRepo   = $courseConfRepo;
    }

    public function getCourseVideos($type, $courseId)
    {
        if ($type != 'all') {
            return $this->model
                ->when($type, function ($q, $type) {
                    return $q->where('subscription_type', $type);
                })
                ->when($courseId, function ($q, $courseId) {
                    return $q->where('id', $courseId);
                })
                ->get();
        } else {
            return $this->model->get();
        }
    }

    public function getCourseDetailsForUpdate($courseId)
    {
        return $this->model->without('educator', 'videos', 'follow', 'enroll', 'reviews', 'total_videos', 'watch_history', 'save_course')->find($courseId);
    }

    public function createCourse($data)
    {
        $data['educator_id']     = Auth::id();
        $data['invitation_link'] = "Test";

        return $this->model->create($data);
    }

    public function enrollCourse($data)
    {
        $data['status'] = 1;
        $course         = $this->courseDetails($data->course_id);

        if (now()->lt(Carbon::parse($course->publish_date))) {
            return "You can't enroll this course before publish";
        }

        if ($course->subscription_type == 'free') {
            $isSubscribe = $this->subscriptionRepo->insertData($data->only('status'));
        } else {
            $data['amount'] = $course->amount;
            $isSubscribe    = $this->subscriptionRepo->stripePayment($data->all());
        }

        if ($isSubscribe) {
            return $this->enrollRepo->insertData([
                'course_id'       => $data->course_id,
                'learner_id'      => Auth::id(),
                'subscription_id' => $isSubscribe->id,
            ]);
        }

        return false;
    }

    public function getCourse()
    {
        return $this->model
            ->where('educator_id', Auth::id())
            ->pluck('id')
            ->all();
    }

    public function updateCourse($courseId, $data)
    {
        $course = $this->findData($courseId);
        if (Auth::user()->cannot('update', $course)) {
            abort(403);
        }

        return $this->updateData(['id' => $courseId, 'educator_id' => Auth::id()], $data);
    }

    public function searchCourse($name = null, $topic = null, $privacy = null, $subscription = null, $tag = null)
    {
        return $this->model
            ->when($name, function ($query, $name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($topic, function ($query, $topic) {
                $query->where('topic_id', $topic);
            })
            ->when($privacy, function ($query, $privacy) {
                $query->where('privacy', $privacy);
            })
            ->when($subscription, function ($query, $subscription) {
                $query->where('subscription_type', $subscription);
            })
            ->when($tag, function ($query, $tag) {
                $query->where('type', $tag);
            })
            ->when(Gate::allows('learner'), function ($q) {
                $q->whereStatus(1);
            })
            ->paginate(15);
    }

    public function getRelatedCourse($topic)
    {
        return $this->model
            ->where('topic_id', $topic)
            ->when(Gate::allows('learner'), function ($q) {
                $q->whereStatus(1);
            })
            ->inRandomOrder()
            ->take(6)
            ->get();
    }

    public function getBestSellingCourses()
    {
        $hotPick = $this->courseTagRepo->whereFirst(['title' => 'hot_pick']);

        return $this->model
            ->having('enroll_student_count', '>=', $hotPick->value)
            ->get();
    }

    public function applyCourseCharge($fee, $amount)
    {
        return $amount - ($amount * $fee / 100);
    }

    public function courseDetails($courseId)
    {
        return $this->model
            ->withoutGlobalScope('privacy')
            ->withoutGlobalScope('publish_date')
            ->find($courseId);
    }

    public function whereInCourse($courses)
    {
        return $this->model
            ->withoutGlobalScope('privacy')
            ->withoutGlobalScope('publish_date')
            ->whereIn("id", $courses)
            ->get();
    }

    public function getCourses($topic)
    {
        return $this->model
            ->whereTopicId($topic)
            ->when(Gate::allows('learner'), function ($query) {
                $query->whereStatus(1);
            })
            ->paginate(15);
    }

    public function validateForAddStudentInCourse($courseId)
    {
        $course = $this->model->find($courseId);

        $msg = '';
        if ($course->status != 1) {
            if ($course->status == 0) {
                $msg = "Unable to add students into inactive course.";
            } elseif ($course->status == 2) {
                $msg = "Unable to add students into suspended course.";
            }
        }

        if (now()->lt(Carbon::parse($course->publish_date))) {
            $msg = "The course has not been published yet!";
        }

        if (!empty($msg)) {
            return $msg;
        }

        return true;
    }

    public function getEnrolledCoursesOfLearner($learnerId, array $educatorCourses)
    {
        return $this->enrollRepo->getEnrolledCoursesOfLearner($learnerId, $educatorCourses);
    }

    public function getEducatorCourses()
    {
        return $this->model
            ->where('educator_id', Auth::id())
            ->pluck('id')
            ->all();
    }
}
