<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Requests\VideoGetRequest;
use App\Http\Requests\VideoOrderingRequest;
use App\Http\Requests\VideoRequest;
use App\Http\Requests\WatchHistoryRequest;
use App\Http\Resources\CourseDetailsResource;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\Video\VideoRepositoryInterface;
use App\Repositories\WatchHistory\WatchHistoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    protected $videoRepo, $watchHistoryRepo, $courseRepo;

    public function __construct(
        VideoRepositoryInterface        $videoRepo,
        WatchHistoryRepositoryInterface $watchHistoryRepo,
        CourseRepositoryInterface       $courseRepo
    )
    {
        $this->videoRepo        = $videoRepo;
        $this->watchHistoryRepo = $watchHistoryRepo;
        $this->courseRepo       = $courseRepo;
    }

    public function getAllVideos(VideoGetRequest $request)
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => $this->videoRepo->getAllVideos($request->all()),
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function createVideo(VideoRequest $request)
    {
        DB::beginTransaction();
        try {
            $videoData                = $request->all();
            $videoData['educator_id'] = Auth::id();

            if ($request->hasFile('video_thumbnail')) {
                $videoData['video_thumbnail'] = $request->video_thumbnail->store('course');
            }

            $isCreate = $this->videoRepo->insertData($videoData);

            DB::commit();
            return response()->json([
                'status' => true,
                'data'   => $isCreate,
            ]);
        }
        catch (\Throwable $th) {
            DB::rollback();
            throw new Exceptions();
        }
    }

    public function updateVideo(VideoRequest $request)
    {
        try {
            $video = $this->videoRepo->findData($request->video_id);
            if ($request->user()->cannot('update', $video)) {
                return Exceptions::forbidden();
            }

            if ($video->update($request->only($this->videoRepo->getFillable()))) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function orderingVideo(VideoOrderingRequest $request)
    {
        try {
            foreach ($request->videos as $key => $video) {
                $this->videoRepo->updateData([
                    'course_id'   => $request->course_id,
                    'educator_id' => Auth::id(),
                    'id'          => $video['video_id'],
                ], ['order_no' => $video['position']]);
            }

            return Exceptions::success();

        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function deleteVideo(Video $video)
    {
        try {
            if (Auth::user()->cannot('update', $video)) {
                return Exceptions::forbidden();
            }

            if ($video->delete()) {
                return Exceptions::success();
            } else {
                return Exceptions::error("Video not found!");
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function getAllVideosByCourse($course)
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => VideoResource::collection($this->videoRepo
                    ->whereGetAndOrderBy([
                        'course_id'   => $course,
                        'educator_id' => Auth::id()
                    ], 'order_no'))
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function addWatchHistory(WatchHistoryRequest $request)
    {
        try {
            if ($history = $this->watchHistoryRepo
                ->whereFirst([
                    'course_id'  => $request->course_id,
                    'video_id'   => $request->video_id,
                    'learner_id' => $request->learner_id,
                ])) {

                if ($history->is_complete) {
                    $history->update(['re_watch_duration' => $request->duration]);
                } else {
                    $history->update($request->only('duration', 'is_complete'));
                }

                return response()->json([
                    'status' => true,
                    'data'   => $history
                ]);
            }

            if ($data = $this->watchHistoryRepo->insertData($request->all())) {
                return response()->json([
                    'status' => true,
                    'data'   => $data
                ]);
            } else {
                return Exceptions::error();
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function getWatchedList()
    {
        try {
            $courses = $this->watchHistoryRepo->getWatchedCourses();
            $data    = $this->courseRepo->whereInCourse($courses);

            $completedCourses = array();
            foreach ($data as $course) {
                $videos = $course->total_videos->pluck('id')->all();

                if ($this->watchHistoryRepo->checkVideoForWatched($videos)) {
                    $completedCourses[] = $course;
                }
            }

            return response()->json([
                'status' => true,
                'data'   => CourseDetailsResource::collection($completedCourses)
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function continueWatching()
    {
        try {
            $courses = $this->watchHistoryRepo->getContinueWatchingCourses();
            $data    = $this->courseRepo->whereInCourse($courses);

            $completedCourses = array();
            foreach ($data as $course) {
                $videos = $course->total_videos->pluck('id')->all();

                if ($this->watchHistoryRepo->checkVideoForContinueWatch($videos)) {
                    $completedCourses[] = $course;
                }
            }

            return response()->json([
                'status' => true,
                'data'   => CourseDetailsResource::collection($completedCourses)
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }
}
