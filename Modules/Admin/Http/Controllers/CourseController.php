<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Notifications\CourseSuspendNotiifcation;
use App\Repositories\Course\CourseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Http\Requests\CourseRequest;

class CourseController extends Controller
{
    private $courseRepo;

    public function __construct(CourseRepositoryInterface $courseRepo)
    {
        $this->courseRepo = $courseRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list()
    {
        $courses = $this->courseRepo->getData(25, 'DESC');
        $courses->load('topic');

        return view('admin::pages.course.list', [
            'courses' => $courses
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $course = $this->courseRepo->findData(decrypt($id));

        $data = [
            'id'                   => encrypt($course->id),
            'educator_id'          => $course->educator_id,
            'educator_name'        => $course->educator->name,
            'topic_id'             => $course->topic_id,
            'topic_name'           => $course->topic->name,
            'privacy'              => $course->privacy,
            'subscription_type'    => $course->subscription_type,
            'amount'               => $course->amount,
            'is_for_kid'           => $course->is_for_kid,
            'project_instructions' => $course->project_instructions,
            'name'                 => $course->name,
            'type'                 => $course->type,
            'description'          => $course->description,
            'publish_date'         => $course->publish_date,
            'image'                => $course->image,
        ];

        return view('admin::pages.course.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param CourseRequest $request
     * @param int $id
     * @return Renderable
     */
    public function update(CourseRequest $request, $id)
    {
        $course  = $this->courseRepo->findData(decrypt($id));
        $reqData = $request->all();

        if ($request->hasFile('image')) {
            $reqData['image'] = $request->image->store('course');
            if (Storage::exists($course->image)) {
                Storage::delete($course->image);
            }
        }

        if ($course->update($reqData)) {
            return ControllerException::success("course.list", "Successfully Updated!");
        } else {
            return ControllerException::error();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete($id)
    {
        try {
            $course = $this->courseRepo->findData(decrypt($id));

            if ($course->enroll_student_count > 0) {
                return Exceptions::error("Unable to delete. Some of students enrolled in this course!!", 412);
            }

            if ($course->delete()) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        }
        catch (\Exception $exception) {
            return Exceptions::error("Something went wrong!!");
        }
    }

    public function suspend($id)
    {
        try {
            $course = $this->courseRepo->findData(decrypt($id));

            if ($course->update(['status' => 2])) {
                $course->educator->notify(new CourseSuspendNotiifcation());
                $course->deleteCourseConstraints();
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        }
        catch (\Exception $exception) {
            return Exceptions::error("Something went wrong!!");
        }
    }
}
