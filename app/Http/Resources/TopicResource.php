<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description ?? "",
            'image'       => $this->image ?? "",
            'is_active'   => $this->is_active,
            'created_at'  => $this->created_at,
            'category'    => new CategoryResource($this->category),
        ];
    }
}
