<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Models\SampleAvatar;
use App\Repositories\AccType\AccTypeRepositoryInterface;
use App\Repositories\AgeType\AgeTypeRepositoryInterface;
use App\Repositories\Ethnicity\EthnicityRepositoryInterface;
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

class UserEthnicityController extends Controller
{
    private $ethnicityRepo;

    public function __construct(EthnicityRepositoryInterface $ethnicityRepo)
    {
        $this->ethnicityRepo = $ethnicityRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list()
    {
        return view('admin::pages.ethnicity.list', [
            'ethnicity' => $this->ethnicityRepo->getData(15, "DESC")
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin::pages.ethnicity.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param UserRoleRequest $request
     * @return Renderable
     */
    public function store(UserRoleRequest $request)
    {
        if ($this->ethnicityRepo->insertData($request->all())) {
            Cache::forget('ethnicity');
            return ControllerException::success("ueth.list");
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
        $ethnicity = $this->ethnicityRepo->findData(decrypt($id));

        $data = [
            'id'          => encrypt($ethnicity->id),
            'name'        => $ethnicity->name,
            'description' => $ethnicity->description,
            'is_active'   => $ethnicity->is_active,
        ];

        return view('admin::pages.ethnicity.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $ethnicity = $this->ethnicityRepo->findData(decrypt($id));

        if ($ethnicity->update($request->all())) {
            Cache::forget('ethnicity');
            return ControllerException::success('ueth.list', 'Successfully updated!');
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
            $ethnicity = $this->ethnicityRepo->findData(decrypt($id));

            if ($ethnicity->delete()) {
                Cache::forget('ethnicity');
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
