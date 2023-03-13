<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Resources\CourseDetailsResource;
use App\Http\Resources\EnrolledCourseResource;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use App\Repositories\WatchHistory\WatchHistoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LearnerController extends Controller
{
    private $enrollRepo, $courseRepo, $watchHistoryRepo;

    /**
     * @param CourseRepositoryInterface $courseRepo
     * @param CourseEnrollRepositoryInterface $enrollRepo
     * @param WatchHistoryRepositoryInterface $watchHistoryRepo
     */
    public function __construct(
        CourseRepositoryInterface       $courseRepo,
        CourseEnrollRepositoryInterface $enrollRepo,
        WatchHistoryRepositoryInterface $watchHistoryRepo
    )
    {
        $this->enrollRepo       = $enrollRepo;
        $this->courseRepo       = $courseRepo;
        $this->watchHistoryRepo = $watchHistoryRepo;
    }

    public function getLearnerCourseData()
    {
        try {
            if (Gate::allows('learner')) {
                // Continue watching
                $courses          = $this->watchHistoryRepo->getContinueWatchingCourses();
                $continueWatching = $this->courseRepo->whereIn("id", $courses);

                // Watched Courses
                $courses        = $this->watchHistoryRepo->getWatchedCourses();
                $watchedCourses = $this->courseRepo->whereIn("id", $courses);

                return response()->json([
                    'status' => true,
                    'data'   => [
                        [
                            "name" => "Enrolled Courses",
                            "data" => EnrolledCourseResource::collection($this->enrollRepo->getLearnerEnrolledCourse())
                        ],
                        [
                            "name" => "Continue Watching",
                            "data" => CourseDetailsResource::collection($continueWatching)
                        ],
                        [
                            "name" => "Watched Courses",
                            "data" => CourseDetailsResource::collection($watchedCourses)
                        ],
                    ]
                ]);
            } else {
                return Exceptions::forbidden();
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }
}
