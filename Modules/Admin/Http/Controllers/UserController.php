<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Notifications\SendMailNotification;
use App\Repositories\AccType\AccTypeRepositoryInterface;
use App\Repositories\AgeType\AgeTypeRepositoryInterface;
use App\Repositories\Ethnicity\EthnicityRepositoryInterface;
use App\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRole\UserRoleRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Http\Requests\UserRequest;

class UserController extends Controller
{
    private $userRepo, $accTypeRepo, $ageTypeRepo, $ethnicityRepo, $passwordResetRepo;

    public function __construct(
        UserRepositoryInterface          $userRepo,
        AccTypeRepositoryInterface       $accTypeRepo,
        AgeTypeRepositoryInterface       $ageTypeRepo,
        EthnicityRepositoryInterface     $ethnicityRepo,
        PasswordResetRepositoryInterface $passwordResetRepo
    )
    {
        $this->userRepo          = $userRepo;
        $this->accTypeRepo       = $accTypeRepo;
        $this->ageTypeRepo       = $ageTypeRepo;
        $this->ethnicityRepo     = $ethnicityRepo;
        $this->passwordResetRepo = $passwordResetRepo;
    }

    public function users()
    {
        return view('admin::pages.user_config.user.list', [
            'users' => $users = $this->userRepo->getUserList()
        ]);
    }

    public function create()
    {
        $data = [
            'accTypes'  => $this->accTypeRepo->whereGet(['is_active' => 1]),
            'ageTypes'  => $this->ageTypeRepo->getData(),
            'ethnicity' => $this->ethnicityRepo->getData(),
        ];

        return view('admin::pages.user_config.user.create', $data);
    }

    public function store(UserRequest $request)
    {
        $insertData             = $request->all();
        $insertData['password'] = Hash::make(time());

        if ($request->hasFile('avatar')) {
            $insertData['avatar'] = $request->avatar->store('avatar');
        }

        if ($user = $this->userRepo->insertData($insertData)) {

            $token = encrypt($user->id);
            $this->passwordResetRepo->insertData([
                'email'      => $user->email,
                'token'      => $token,
                'created_at' => now()
            ]);

            $user->notify(new SendMailNotification(true, $token));
            return ControllerException::success('user.list');
        } else {
            return ControllerException::error();
        }
    }

    public function edit($id)
    {
        $user = $this->userRepo->findData(decrypt($id));

        $data = [
            'accTypes'  => $this->accTypeRepo->whereGet(['is_active' => 1]),
            'ageTypes'  => $this->ageTypeRepo->getData(),
            'ethnicity' => $this->ethnicityRepo->getData(),

            'id'           => encrypt($user->id),
            'acc_type_id'  => $user->acc_type_id,
            'age_type_id'  => $user->age_type_id,
            'ethnicity_id' => $user->ethnicity_id,
            'name'         => $user->name,
            'email'        => $user->email,
            'phone'        => $user->phone,
            'birth_date'   => $user->birth_date,
            'avatar'       => $user->avatar,
        ];

        return view('admin::pages.user_config.user.edit', $data);
    }

    public function update(UserRequest $request, $id)
    {
        $user       = $this->userRepo->findData(decrypt($id));
        $updateData = $request->all();

        if ($request->hasFile('avatar')) {
            $updateData['avatar'] = $request->avatar->store('avatar');
            if (Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }
        }

        if ($user->update($updateData)) {
            return ControllerException::success('user.list');
        } else {
            return ControllerException::error();
        }
    }

    public function delete($id)
    {
        $user = $this->userRepo->findData(decrypt($id));

        if ($user->acc_type_id === 5) {
            return Exceptions::error("Unable to delete admin account!");
        }

        if ($user->delete()) {
            return Exceptions::success();
        } else {
            return Exceptions::error();
        }
    }
}
