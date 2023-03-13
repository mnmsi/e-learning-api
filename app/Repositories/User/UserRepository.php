<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getChildren()
    {
        return Auth::user()->load('children');
    }

    public function getUserList()
    {
        return $this->model
            ->where('is_acc_type_update', 1)
            ->orderBy('id', 'DESC')
            ->paginate(25);
    }

    public function updatePassword($data)
    {
        $user = Auth::user();
        if (Hash::check($data['old_password'], $user->password)) {
            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);

            return $user;
        }

        return false;
    }

    public function getCountryUsers()
    {
        return $this->model
            ->select('country', DB::raw('COUNT(country) as total_country'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->pluck('total_country', 'country')
            ->all();
    }

    public function whereInUser($usersId)
    {
        return $this->model
            ->whereIn('id', $usersId)
            ->where('push_notification', 1)
            ->get();
    }

}
