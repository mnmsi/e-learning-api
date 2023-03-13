<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Models\SampleAvatar;
use App\Repositories\UserRole\UserRoleRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\UserRoleRequest;

class UserRoleController extends Controller
{
    private $userRoleRepo;

    public function __construct(UserRoleRepositoryInterface $userRoleRepo)
    {
        $this->userRoleRepo = $userRoleRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list()
    {
        return view('admin::pages.user_role.list', [
            'roles' => $this->userRoleRepo->getData(15, "DESC")
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin::pages.user_role.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param UserRoleRequest $request
     * @return Renderable
     */
    public function store(UserRoleRequest $request)
    {
        if ($this->userRoleRepo->insertData($request->all())) {
            return ControllerException::success("ur.list");
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
        $avatar = $this->userRoleRepo->findData(decrypt($id));

        $data = [
            'id'          => encrypt($avatar->id),
            'name'        => $avatar->name,
            'description' => $avatar->description,
            'is_active'   => $avatar->is_active,
        ];

        return view('admin::pages.user_role.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $avatar = $this->userRoleRepo->findData(decrypt($id));

        if ($avatar->update($request->all())) {
            return ControllerException::success('ur.list', 'Successfully updated!');
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
            $role = $this->userRoleRepo->findData(decrypt($id));

            if ($role->delete()) {
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
