<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Requests\AnnouncementRequest;
use App\Repositories\CourseAnnouncement\CourseAnnouncementRepositoryInterface;
use Illuminate\Support\Facades\Gate;

class AnnouncementController extends Controller
{
    protected $announcementRepo;

    public function __construct(CourseAnnouncementRepositoryInterface $announcementRepo)
    {
        $this->announcementRepo = $announcementRepo;
    }

    public function getAll()
    {
        if (Gate::allows('educator')) {
            return response()->json([
                'status' => true,
                'data'   => $this->announcementRepo->getAll()
            ]);
        } else {
            return Exceptions::forbidden();
        }
    }

    public function getAnnouncement($courseId)
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => $this->announcementRepo->whereGet(['course_id' => $courseId]),
            ]);
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function create(AnnouncementRequest $request)
    {
        try {
            if ($data = $this->announcementRepo->insertData($request->all())) {
                return response()->json([
                    'status' => true,
                    'data'   => $data,
                ]);
            } else {
                return Exceptions::error();
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function delete($id)
    {
        try {
            $announcement = $this->announcementRepo->findData($id);

            if ($announcement) {
                if ($announcement->delete()) {
                    return Exceptions::success();
                } else {
                    return Exceptions::error();
                }
            } else {
                return Exceptions::error();
            }
        }
        catch (\Throwable $th) {
            throw new Exceptions();
        }
    }
}
