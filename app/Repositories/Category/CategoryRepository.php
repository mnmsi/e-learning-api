<?php

namespace App\Repositories\Category;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function getCategories()
    {
        return Cache::rememberForever('categories', function () {
            return $this->model
                ->where('is_active', 1)
                ->get();
        });
    }
}
