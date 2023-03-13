<?php

namespace App\Http\Resources;

use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class StudentInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'birth_date'     => $this->birth_date,
            'avatar'         => $this->avatar,
            'country'        => $this->country,
            'acc_type'       => $this->acc_type,
            'age_type'       => $this->age_type,
            'ethnicity'      => $this->ethnicity,
            'students_count' => ((resolve(CourseEnrollRepositoryInterface::class))->getStudents($this->id))->count(),
            'course_count'   => $this->course_info_count
        ];
    }
}
