<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Models\SampleAvatar;
use App\Repositories\SampleAvatar\SampleAvatarRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Http\Requests\SampleAvatarRequest;

class SampleAvatarController extends Controller
{
    private $avatarRepository;

    public function __construct(SampleAvatarRepositoryInterface $avatarRepository)
    {
        $this->avatarRepository = $avatarRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list()
    {
        return view('admin::pages.sample_avatar.list', [
            'sampleAvatars' => $this->avatarRepository->getData(15, "DESC")
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin::pages.sample_avatar.create');
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
            $insertData['image'] = $request->image->store('sample_avatars');
            $this->avatarRepository->insertData($insertData);
        }
        catch (\Exception $exception) {
            throw new ControllerException();
        }

        Cache::forget('sample_avatar');
        return ControllerException::success("sa.list");
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $avatar = $this->avatarRepository->findData(decrypt($id));

        $data = [
            'id'          => encrypt($avatar->id),
            'name'        => $avatar->name,
            'description' => $avatar->description,
            'image'       => $avatar->image,
            'is_active'       => $avatar->is_active,
        ];

        return view('admin::pages.sample_avatar.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $avatar     = $this->avatarRepository->findData(decrypt($id));
        $updateData = $request->all();

        if ($request->hasFile('image')) {
            $updateData['image'] = $request->image->store('sample_avatars');
            if (Storage::exists($avatar->image)) {
                Storage::delete($avatar->image);
            }
        }

        if ($avatar->update($updateData)) {
            Cache::forget('sample_avatar');
            return ControllerException::success('sa.list', 'Successfully updated!');
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
            $avatar = $this->avatarRepository->findData(decrypt($id));

            if (Storage::exists($avatar->image)) {
                Storage::delete($avatar->image);
            }

            if ($avatar->delete()) {
                Cache::forget('sample_avatar');
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
