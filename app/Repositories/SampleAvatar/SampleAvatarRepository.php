<?php

namespace App\Repositories\SampleAvatar;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class SampleAvatarRepository extends BaseRepository implements SampleAvatarRepositoryInterface
{
    public function getSampleAvatars()
    {
        return Cache::rememberForever('sample_avatar', function () {
            return $this->model->where('is_active', 1)->get();
        });
    }
}
