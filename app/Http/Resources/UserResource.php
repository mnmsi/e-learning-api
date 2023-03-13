<?php

namespace App\Http\Resources;

use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'birth_date'        => $this->birth_date,
            'avatar'            => $this->avatar,
            'about_me'          => $this->about_me ?? "",
            'device_id'         => $this->device_id ?? "",
            'interested_topics' => InterestedTopicResource::collection($this->interested_topic),
        ];

        if (is_null($this->user_parent_id)) {
            $data['acc_type']  = new AccTypeResource($this->acc_type);
            $data['age_type']  = new AgeTypeResource($this->age_type);
            $data['ethnicity'] = new EthnicityResource($this->ethnicity);
        } else {
            $data['acc_type']  = new AccTypeResource($this->parent_acc->acc_type);
            $data['age_type']  = new AgeTypeResource($this->parent_acc->age_type);
            $data['ethnicity'] = new EthnicityResource($this->parent_acc->ethnicity);
            $data['child_age'] = $this->child_age ?? "";
        }

        return $data;
    }

    public function userBasicInfo()
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'avatar'         => $this->avatar,
            'total_students' => ((resolve(CourseEnrollRepositoryInterface::class))->getStudents($this->id))->count(),
        ];
    }
}
