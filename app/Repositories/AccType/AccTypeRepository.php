<?php

namespace App\Repositories\AccType;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class AccTypeRepository extends BaseRepository implements AccTypeRepositoryInterface
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
