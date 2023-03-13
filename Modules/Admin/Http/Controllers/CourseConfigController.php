<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Repositories\CourseConf\CourseConfRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\CourseConfigRequest;

class CourseConfigController extends Controller
{
    private $courseConfRepo;

    public function __construct(CourseConfRepositoryInterface $courseConfRepo)
    {
        $this->courseConfRepo = $courseConfRepo;
    }

    public function conf()
    {
        return view('admin::pages.course_conf.list', [
            'conf' => $this->courseConfRepo->getConf()
        ]);
    }

    public function update(CourseConfigRequest $request)
    {
        foreach ($request->data as $title => $reqData) {
            $conf = $this->courseConfRepo->updateConf($title, ['value' => $reqData]);
        }

        return ControllerException::success("course.config", "Successfully Updated!");
    }
}
