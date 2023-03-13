<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscussionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'course_id'        => $this->course_id,
            'parent_id'        => $this->parent_id,
            'comment'          => $this->comment,
            'updated_at'       => $this->updated_at,
            'user_like'        => (new DiscussionLikeResource($this->user_like))->isUserLike(),
            'total_like_count' => $this->total_like_count,
            'user'             => (new UserResource($this->user))->userBasicInfo(),
        ];

        if (count($this->sub_comment) > 0) {
            $data['sub_comment'] = DiscussionResource::collection($this->sub_comment);
        }

        return $data;
    }
}
