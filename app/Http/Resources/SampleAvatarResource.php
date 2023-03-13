<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SampleAvatarResource extends JsonResource
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
            'id'          => $this->id,
            'name'        => is_null($this->name) ? '' : $this->name,
            'description' => is_null($this->description) ? '' : $this->description,
            'image'       => $this->image_path,
            'image_path'  => $this->image,
        ];
    }
}
