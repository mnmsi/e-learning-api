<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchCourseResource extends JsonResource
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
            'id'                    => $this->id,
            'educator_id'           => $this->educator_id,
            'topic_id'              => $this->topic_id,
            'privacy'               => $this->privacy,
            'subscription_type'     => $this->subscription_type,
            'amount'                => (double)$this->amount ?? "",
            'is_for_kid'            => $this->is_for_kid,
            'project_instructions'  => $this->project_instructions,
            'name'                  => $this->name,
            'type'                  => $this->type,
            'description'           => $this->description,
            'publish_date'          => Carbon::parse($this->publish_date)->format('d/m/Y'),
            'image'                 => $this->image,
            'invitation_link'       => $this->invitation_link ?? "",
            'is_suspend'            => $this->status == 2 ? true : false,
            'updated_at'            => $this->updated_at,
            'created_at'            => $this->created_at,
            'is_follow'             => (new FollowResource($this->follow))->isFollow(),
            'is_save'               => (new SaveCourseResource($this->save_course))->isSave(),
            'is_enrolled'           => (new EnrolledResource($this->enroll))->isEnrolled(),
            'total_enroll_students' => $this->enroll_student_count,
            'total_videos'          => $this->total_videos_count,
            'last_watched'          => (new CourseResource($this))->getLastWatchedTime(),
            'total_duration'        => $this->total_videos->sum('duration'),
            'complete_percent'      => (new CourseResource($this))->getCompleteStatus(),
            'tags'                  => $this->tags,
            'educator'              => (new UserResource($this->educator))->userBasicInfo(),
            'reviews'               => (new ReviewListCollection($this->reviews))->getOnlyReview()
        ];
    }
}
