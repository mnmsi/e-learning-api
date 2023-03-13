<?php

namespace App\Repositories\CourseConf;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class CourseConfRepository extends BaseRepository implements CourseConfRepositoryInterface
{
    public function getConf()
    {
        return $this->model->get();
    }

    public function findConf($title)
    {
        return $this->model
            ->where('title', $title)
            ->first();
    }

    public function getCourseFee()
    {
        return $this->model
            ->where('title', 'course_fee')
            ->first()
            ->value;
    }

    public function updateConf($title, $data)
    {
        return $this->model
            ->where('title', $title)
            ->update($data);
    }
}
