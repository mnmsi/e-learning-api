<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FollowResource extends JsonResource
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
            'id'                 => $this->id,
            'educator_id'        => $this->educator_id,
            'educator_name'      => $this->educator->name,
            'educator_email'     => $this->educator->email,
            'educator_avatar'    => $this->educator->avatar,
            'educator_role_id'   => $this->educator->acc_type->role->id,
            'educator_role_name' => $this->educator->acc_type->role->name,
            'educator_acc_type'  => $this->educator->acc_type->name,
            'followed_at'        => $this->created_at,
        ];
    }

    public function isFollow()
    {
        return $this->is_follow ?? false;
    }
}
