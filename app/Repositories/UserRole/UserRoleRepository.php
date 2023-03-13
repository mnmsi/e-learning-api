<?php

namespace App\Repositories\UserRole;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class UserRoleRepository extends BaseRepository implements UserRoleRepositoryInterface
{
    public function getAccTypes($role_id)
    {
        return Cache::rememberForever('acc_types_' . $role_id, function () use ($role_id) {
            return $this->model
                ->where('role_id', $role_id)
                ->where('is_active', 1)
                ->get();
        });
    }
}
