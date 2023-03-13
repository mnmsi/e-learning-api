<?php

namespace App\Repositories\Ethnicity;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class EthnicityRepository extends BaseRepository implements EthnicityRepositoryInterface
{
    public function getEthnicity()
    {
        return Cache::rememberForever('ethnicity', function () {
            return $this->model->where('is_active', 1)->orderBy('name')->get();
        });
    }
}
