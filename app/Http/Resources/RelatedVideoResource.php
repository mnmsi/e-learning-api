<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RelatedVideoResource extends JsonResource
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
            'id'                => $this->id,
            'topic_id'          => $this->topic_id,
            'privacy'           => $this->privacy,
            'subscription_type' => $this->subscription_type,
            'amount'            => (double)$this->amount ?? "",
            'is_for_kid'        => $this->is_for_kid,
            'name'              => $this->name,
            'type'              => $this->type,
            'publish_date'      => $this->publish_date,
            'image'             => $this->image,
            'invitation_link'   => $this->invitation_link ?? "",
            'created_at'        => $this->created_at,
            'tags'              => $this->tags,
            'educator'          => (new UserResource($this->educator))->userBasicInfo(),
            'reviews'           => (new ReviewListCollection($this->reviews))->getOnlyReview(),
        ];
    }
}
