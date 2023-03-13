<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Repositories\Video\VideoRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Http\Requests\VideoRequest;

class VideoController extends Controller
{
    private $videoRepo;

    public function __construct(VideoRepositoryInterface $videoRepo)
    {
        $this->videoRepo = $videoRepo;
    }

    public function getVideo($courseId)
    {
        try {
            return view('admin::pages.video.list_table', [
                'videos' => $this->videoRepo->whereGet(['course_id' => decrypt($courseId)])
            ]);
        }
        catch (\Exception $ex) {
            throw new Exceptions();
        }
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list(Request $request)
    {
        return view('admin::pages.video.list', [
            'video' => $this->videoRepo->getData(25, 'DESC')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $video = $this->videoRepo->findData(decrypt($id));

        $data = [
            'id'              => encrypt($video->id),
            'course_id'       => $video->course_id,
            'title'           => $video->title,
            'description'     => $video->description,
            'location'        => $video->location,
            'video_thumbnail' => $video->video_thumbnail,
            'video_url'       => $video->video_path,
            'order_no'        => $video->order_no,
            'duration'        => $video->duration,
        ];

        return view('admin::pages.video.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param VideoRequest $request
     * @param int $id
     * @return Renderable
     */
    public function update(VideoRequest $request, $id)
    {
        $video   = $this->videoRepo->findData(decrypt($id));
        $reqData = $request->all();

        if ($request->hasFile('video_thumbnail')) {
            if (Storage::exists($video->video_thumbnail)) {
                Storage::delete($video->video_thumbnail);
            }
        }

        if ($video->update($reqData)) {
            return ControllerException::success('course.list', 'Successfully Updated!');
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
            $video = $this->videoRepo->findData(decrypt($id));

            if ($video->delete()) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        }
        catch (\Exception $exception) {
            throw new Exceptions("Something went wrong!!");
        }
    }
}
