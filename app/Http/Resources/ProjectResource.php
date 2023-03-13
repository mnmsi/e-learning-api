<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'id'                  => $this->id,
            'course_id'           => $this->course_id,
            'caption'             => $this->caption ?? '',
            'type'                => $this->type ?? '',
            'media'               => $this->media_path ?? '',
            'media_path'          => $this->media ?? '',
            'thumbnail'           => $this->thumbnail ?? '',
            'is_like'             => (new ProjectLikeResource($this->learner_like))->isLearnerLike(),
            'total_project_likes' => $this->project_like_count,
            'updated_at'          => $this->updated_at,
            'learner'             => (new UserResource($this->learner))->userBasicInfo(),
            'course'              => (new CourseResource($this->course))->shortInfo(),
        ];
    }
}
