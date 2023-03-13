<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EducatorStudentList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $learnerInfo  = (new UserResource($this->learner))->userBasicInfo();
        $enrollCourse = [
            'id'         => $this->id,
            'course_id'  => $this->course_id,
            'created_at' => $this->created_at,
        ];

        return array_merge($enrollCourse, $learnerInfo);
    }
}
