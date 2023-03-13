<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Requests\AddStudentRequest;
use App\Http\Requests\CourseGetRequest;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\CourseSaveRequest;
use App\Http\Requests\EnrollRequest;
use App\Http\Resources\CourseDetailsResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CoursesByCategoryResource;
use App\Http\Resources\EnrolledCourseResource;
use App\Http\Resources\RelatedVideoResource;
use App\Http\Resources\SaveCourseResource;
use App\Http\Resources\SearchCourseResource;
use App\Http\Resources\SearchRecommendationResource;
use App\Imports\AddStudentImport;
use App\Notifications\SendMailNotification;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Repositories\SaveCourse\SaveCourseRepositoryInterface;
use App\Repositories\StudentAddJob\StudentAddJobRepositoryInterface;
use App\Repositories\Subscription\SubscriptionRepositoryInterface;
use App\Repositories\Topic\TopicRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CourseController extends Controller
{
    protected $courseRepo, $topicRepo, $saveCourseRepo, $enrollRepo, $userRepo, $subscriptionRepo, $passwordResetRepo, $studentAddJobRepo;

    public function __construct(
        CourseRepositoryInterface        $courseRepo,
        TopicRepositoryInterface         $topicRepo,
        SaveCourseRepositoryInterface    $saveCourseRepo,
        CourseEnrollRepositoryInterface  $enrollRepo,
        UserRepositoryInterface          $userRepo,
        SubscriptionRepositoryInterface  $subscriptionRepo,
        PasswordResetRepositoryInterface $passwordResetRepo,
        StudentAddJobRepositoryInterface $studentAddJobRepo
    )
    {
        $this->courseRepo        = $courseRepo;
        $this->topicRepo         = $topicRepo;
        $this->saveCourseRepo    = $saveCourseRepo;
        $this->enrollRepo        = $enrollRepo;
        $this->userRepo          = $userRepo;
        $this->subscriptionRepo  = $subscriptionRepo;
        $this->passwordResetRepo = $passwordResetRepo;
        $this->studentAddJobRepo = $studentAddJobRepo;
    }

    public function getCourse(CourseGetRequest $request)
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => CoursesByCategoryResource::collection($this->topicRepo->getCourseByTopic($request->all()), true),
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function getEnrollCourses()
    {
        try {
            if (Gate::allows('learner')) {
                return response()->json([
                    'status' => true,
                    'data'   => EnrolledCourseResource::collection($this->enrollRepo->getLearnerEnrolledCourse()),
                ]);
            } else {
                return Exceptions::forbidden();
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function getCourseDetails($courseId)
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => new CourseResource($this->courseRepo->courseDetails($courseId)),
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function getCourseDetailsForUpdate($courseId)
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => (new CourseResource($this->courseRepo->getCourseDetailsForUpdate($courseId)))->courseInfo(),
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function createCourse(CourseRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($data = $this->courseRepo->createCourse($request->all())) {
                DB::commit();
                return response()->json([
                    'status' => true,
                    'data'   => $data,
                ]);
            } else {
                DB::rollback();
                return Exceptions::error();
            }
        }
        catch (\Throwable $th) {
            DB::rollback();
            throw new Exceptions();
        }
    }

    public function updateCourse(CourseRequest $request)
    {
        try {
            $course = $this->courseRepo->findData($request->course_id);
            if ($request->user()->cannot('update', $course)) {
                return Exceptions::forbidden();
            }

            if ($course->update($request->only($this->courseRepo->getFillable()))) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function enrollCourse(EnrollRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($enroll = $this->courseRepo->enrollCourse($request)) {

                if (is_string($enroll)) {
                    return Exceptions::error($enroll);
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'data'   => $enroll,
                ]);
            } else {
                DB::rollback();
                return Exceptions::error();
            }
        }
        catch (\Throwable $th) {
            DB::rollback();
            return Exceptions::error(explode('. ', $th->getMessage())[0] ?? $th->getMessage());
        }
    }

    public function saveCourse(CourseSaveRequest $request)
    {
        try {
            if ($this->saveCourseRepo->saveCourse($request->only('course_id', 'save'))) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function saveCourseList()
    {
        try {
            if (Gate::allows('learner')) {
                return response()->json([
                    'status' => true,
                    'data'   => SaveCourseResource::collection($this->saveCourseRepo->getLearnerSavedCourse()),
                ]);
            } else {
                return Exceptions::forbidden();
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function addStudent(AddStudentRequest $request)
    {
        DB::beginTransaction();
        try {
            $exRow = (Excel::toArray(new AddStudentImport, $request->file('file')))[0];

            if (count($exRow) == 0) {
                return Exceptions::error("Your file is empty!");
            }

            if (count($exRow) > 20) {
                return Exceptions::error("You can't add more than 20 students");
            }

            $reqData         = $request->all();
            $reqData['file'] = $request->file->store('student_add');

            $this->studentAddJobRepo->insertData($reqData);
        }
        catch (\Throwable $th) {
            DB::rollBack();
            throw new Exceptions();
        }

        DB::commit();
        return Exceptions::success();
    }

    public function getCourseByTopic($topic)
    {
        try {
            return response()->json([
                'status'  => true,
                //                'courses' => (CourseResource::collection($this->courseRepo->wherePaginate(['topic_id' => $topic], 15)))->response()->getData()
                'courses' => (CourseResource::collection($this->courseRepo->getCourses($topic)))->response()->getData()
            ]);
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }

    public function searchCourse(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'         => 'nullable|string',
                'topic_id'     => 'nullable|integer',
                'privacy'      => 'nullable|in:private,public',
                'subscription' => 'nullable|in:free,paid',
                'course_tag'   => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return Exceptions::validationError($validator->errors()->all());
            }

            return response()->json([
                'status'  => true,
                'courses' => SearchCourseResource::collection($this->courseRepo->searchCourse(
                    $request->name,
                    $request->category,
                    $request->privacy,
                    $request->subscription,
                    $request->tag
                ))->response()->getData()
            ]);
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }

    public function searchRecommendation()
    {
        try {
            $categories = $this->topicRepo->getData();

            return response()->json([
                'status' => true,
                'data'   => SearchRecommendationResource::collection($categories)
            ]);
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }

    public function getEducatorAllCourse()
    {
        try {
            if (Gate::allows('educator')) {
                return response()->json([
                    'status' => true,
                    'data'   => CourseDetailsResource::collection($this->courseRepo->whereGet(['educator_id' => Auth::id()]))
                ]);
            } else {
                return Exceptions::forbidden();
            }
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }

    public function getRelatedCourse($topicId)
    {
        try {
            return response()->json([
                'status'  => true,
                'courses' => RelatedVideoResource::collection($this->courseRepo->getRelatedCourse($topicId))
            ]);
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }

    public function deleteCourse($id)
    {
        DB::beginTransaction();
        try {
            if (Gate::allows('educator')) {
                if ($course = $this->courseRepo->findData($id)) {
                    if ($course->enroll_student_count > 0) {
                        return Exceptions::error("Unable to delete. Some of students enrolled in this course!!", 412);
                    }

                    if ($course->delete()) {
                        DB::commit();
                        return Exceptions::success();
                    } else {
                        return Exceptions::error("Something went wrong!!", 412);
                    }
                } else {
                    return Exceptions::error("Invalid course id!", 404);
                }
            } else {
                return Exceptions::forbidden();
            }
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return Exceptions::error("Something went wrong!!");
        }
    }

    public function getLearnNext()
    {
        try {
            $data = $this->topicRepo->getWhatLearnNext();

            return response()->json([
                'status'  => true,
                'courses' => CourseDetailsResource::collection($data->course)
            ]);
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }

    public function checkName(Request $request)
    {
        try {
            if ($this->courseRepo->whereExists([
                'educator_id' => Auth::id(),
                'name'        => $request->name
            ])) {
                return response()->json([
                    'status' => true,
                ]);
            }

            return Exceptions::error("Course already been created by the given name. You need to use another course name.", 422);
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }

    public function getBestSellerCourses()
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => CourseDetailsResource::collection($this->courseRepo->getBestSellingCourses())
            ]);
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }

    public function removeLearner($learnerId)
    {
        try {
            if (!Gate::allows('educator')) {
                return Exceptions::forbidden();
            }

            $learner = $this->userRepo->findData($learnerId);
            if (!$learner) {
                return Exceptions::error("Invalid learner");
            }

            $educatorCourses = $this->courseRepo->getEducatorCourses();
            $courseEnroll    = $this->courseRepo->getEnrolledCoursesOfLearner($learnerId, $educatorCourses);

            if ($courseEnroll->count() == 0) {
                return Exceptions::error("The learner not enrolled in the selected course");
            }

            $courseEnroll->map(function ($enroll) {
                if ($enroll->course->subscription_type === 'paid') {
                    if ($enroll->subscription->stripe_charge) {
                        if (!$this->subscriptionRepo->refund($enroll->subscription->stripe_charge)) {
                            return Exceptions::error("Unable to refund");
                        }
                    }
                }

                $enroll->delete();
                if ($enroll->subscription) {
                    $enroll->subscription->delete();
                }

                return true;
            });

            return Exceptions::success();
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }
}
