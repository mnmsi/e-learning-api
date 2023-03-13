<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewListResource extends JsonResource
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
            'learner_id'  => $this->learner_id,
            'rate'        => $this->rate,
            'review_text' => $this->review_text,
            'created_at'  => $this->created_at,
            'learner'     => (new UserResource($this->learner))->userBasicInfo(),
        ];
    }

}
