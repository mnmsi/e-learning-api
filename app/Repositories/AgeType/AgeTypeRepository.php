<?php

namespace App\Repositories\AgeType;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class AgeTypeRepository extends BaseRepository implements AgeTypeRepositoryInterface
{
    public function getAgeTypes()
    {
        return Cache::rememberForever('age_types', function () {
            return $this->model->where('is_active', 1)->get();
        });
    }
}
