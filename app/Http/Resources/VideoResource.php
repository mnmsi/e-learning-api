<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class VideoResource extends JsonResource
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
            'id'              => $this->id,
            'educator_id'     => $this->educator_id,
            'course_id'       => $this->course_id,
            'title'           => $this->title,
            'description'     => $this->description,
            'location'        => $this->location,
            'video_thumbnail' => $this->video_thumbnail,
            'video_path'      => $this->video_url, //From DB
            'video_url'       => $this->video_path, //Add Url From Model
            'duration'        => $this->duration,
            'order_no'        => $this->order_no,
            'updated_at'      => $this->updated_at,
            "created_at"      => $this->created_at,
        ];
    }

    public function getTotalDuration()
    {
        return $this->sum('duration');
    }
}
