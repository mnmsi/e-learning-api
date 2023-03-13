<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Models\SampleAvatar;
use App\Repositories\AccType\AccTypeRepositoryInterface;
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

class UserAccTypeController extends Controller
{
    private $accTypeRepo, $userRoleRepository;

    public function __construct(
        AccTypeRepositoryInterface  $accTypeRepo,
        UserRoleRepositoryInterface $userRoleRepository
    )
    {
        $this->accTypeRepo        = $accTypeRepo;
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list()
    {
        return view('admin::pages.acc_type.list', [
            'accTypes' => $this->accTypeRepo->getData(15, "DESC")
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin::pages.acc_type.create', [
            'roles' => $this->userRoleRepository->getData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param AccTypeRequest $request
     * @return Renderable
     */
    public function store(AccTypeRequest $request)
    {
        if ($this->accTypeRepo->insertData($request->all())) {
            Cache::forget('acc_types_1');
            Cache::forget('acc_types_2');
            return ControllerException::success("uat.list");
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
        $avatar = $this->accTypeRepo->findData(decrypt($id));

        $data = [
            'id'          => encrypt($avatar->id),
            'name'        => $avatar->name,
            'description' => $avatar->description,
            'role_id'     => $avatar->role_id,
            'is_active'   => $avatar->is_active,
            'roles'       => $this->userRoleRepository->getData()
        ];

        return view('admin::pages.acc_type.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $avatar = $this->accTypeRepo->findData(decrypt($id));

        if ($avatar->update($request->all())) {
            Cache::forget('acc_types_1');
            Cache::forget('acc_types_2');
            return ControllerException::success('uat.list', 'Successfully updated!');
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
            $accType = $this->accTypeRepo->findData(decrypt($id));

            if ($accType->delete()) {
                Cache::forget('acc_types_1');
                Cache::forget('acc_types_2');
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
