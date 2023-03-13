<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CoursesByCategoryResource extends JsonResource
{
    private $filter;

    public function __construct($resource, $filter)
    {
        parent::__construct($resource);
        $this->filter = $filter;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request, $filter = false)
    {
        $course = CourseDetailsResource::collection($this->course);

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description ?? "",
            'created_at'  => $this->craeted_at,
            'course'      => $course,
            //            'course'      => !$this->filter ? $course : collect($course->whereNull('enroll'))->all(),
        ];
    }
}
