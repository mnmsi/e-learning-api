<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Models\SampleAvatar;
use App\Repositories\AccType\AccTypeRepositoryInterface;
use App\Repositories\AgeType\AgeTypeRepositoryInterface;
use App\Repositories\SampleAvatar\SampleAvatarRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRole\UserRoleRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Http\Requests\AccTypeRequest;
use Modules\Admin\Http\Requests\SampleAvatarRequest;
use Modules\Admin\Http\Requests\UserRoleRequest;

class UserAgeTypeController extends Controller
{
    private $ageTypeRepo;

    public function __construct(AgeTypeRepositoryInterface $ageTypeRepo)
    {
        $this->ageTypeRepo = $ageTypeRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list()
    {
        return view('admin::pages.age_type.list', [
            'ageTypes' => $this->ageTypeRepo->getData(15, "DESC")
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin::pages.age_type.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param UserRoleRequest $request
     * @return Renderable
     */
    public function store(UserRoleRequest $request)
    {
        if ($this->ageTypeRepo->insertData($request->all())) {
            Cache::forget('age_types');
            return ControllerException::success("uagt.list");
        }

        return ControllerException::error();
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
        $ageType = $this->ageTypeRepo->findData(decrypt($id));

        $data = [
            'id'          => encrypt($ageType->id),
            'name'        => $ageType->name,
            'description' => $ageType->description,
            'is_active'   => $ageType->is_active,
        ];

        return view('admin::pages.age_type.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $ageType = $this->ageTypeRepo->findData(decrypt($id));

        if ($ageType->update($request->all())) {
            Cache::forget('age_types');
            return ControllerException::success('uagt.list', 'Successfully updated!');
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
            $ageType = $this->ageTypeRepo->findData(decrypt($id));

            if ($ageType->delete()) {
                Cache::forget('age_types');
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
