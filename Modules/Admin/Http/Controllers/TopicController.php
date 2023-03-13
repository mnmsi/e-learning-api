<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Repositories\Topic\TopicRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Http\Requests\SampleAvatarRequest;

class TopicController extends Controller
{
    private $topicRepo;

    public function __construct(TopicRepositoryInterface $topicRepo)
    {
        $this->topicRepo = $topicRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list()
    {
        return view('admin::pages.topic.list', [
            'topics' => $this->topicRepo->getData(15, "DESC")
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin::pages.topic.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param SampleAvatarRequest $request
     * @return Renderable
     */
    public function store(SampleAvatarRequest $request)
    {
        try {
            $insertData          = $request->all();
            $insertData['image'] = $request->image->store('topics');
            $this->topicRepo->insertData($insertData);
        }
        catch (\Exception $exception) {
            throw new ControllerException();
        }

        Cache::forget('topics');
        return ControllerException::success("topic.list");
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $avatar = $this->topicRepo->findData(decrypt($id));

        $data = [
            'id'          => encrypt($avatar->id),
            'name'        => $avatar->name,
            'description' => $avatar->description,
            'image'       => $avatar->image,
            'is_active'   => $avatar->is_active,
        ];

        return view('admin::pages.topic.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $avatar     = $this->topicRepo->findData(decrypt($id));
        $updateData = $request->all();

        if ($request->hasFile('image')) {
            $updateData['image'] = $request->image->store('topics');
            if (Storage::exists($avatar->image)) {
                Storage::delete($avatar->image);
            }
        }

        if ($avatar->update($updateData)) {
            Cache::forget('topics');
            return ControllerException::success('topic.list', 'Successfully updated!');
        }

        return ControllerException::error();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete($id)
    {
        try {
            $avatar = $this->topicRepo->findData(decrypt($id));

            if (Storage::exists($avatar->image)) {
                Storage::delete($avatar->image);
            }

            if ($avatar->delete()) {
                Cache::forget('topics');
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }
}
