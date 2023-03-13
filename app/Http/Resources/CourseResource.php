<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'updated_at'            => $this->updated_at,
            'is_suspend'            => $this->status == 2 ? true : false,
            'created_at'            => $this->created_at,
            'is_follow'             => (new FollowResource($this->follow))->isFollow(),
            'is_save'               => (new SaveCourseResource($this->save_course))->isSave(),
            'is_enrolled'           => (new EnrolledResource($this->enroll))->isEnrolled(),
            'total_enroll_students' => $this->enroll_student_count,
            'total_videos'          => $this->total_videos_count,
            'last_watched'          => $this->getLastWatchedTime(),
            'total_duration'        => $this->total_videos->sum('duration'),
            'complete_percent'      => $this->getCompleteStatus(),
            'tags'                  => $this->tags,
            'educator'              => (new UserResource($this->educator))->userBasicInfo(),
            'videos'                => VideoResource::collection($this->videos),
            'reviews'               => (new ReviewListCollection($this->reviews))->getOnlyReview()
        ];
    }

    public function shortInfo()
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
            'updated_at'            => $this->updated_at,
            'created_at'            => $this->created_at,
            'is_follow'             => (new FollowResource($this->follow))->isFollow(),
            'is_save'               => (new SaveCourseResource($this->save_course))->isSave(),
            'is_enrolled'           => (new EnrolledResource($this->enroll))->isEnrolled(),
            'total_enroll_students' => $this->enroll_student_count,
            'total_videos'          => $this->total_videos_count,
            'tags'                  => $this->tags,
            'educator'              => (new UserResource($this->educator))->userBasicInfo(),
            'reviews'               => (new ReviewListCollection($this->reviews))->getOnlyReview()
        ];
    }

    public function courseInfo()
    {
        return [
            "id"                   => $this->id,
            "educator_id"          => $this->educator_id,
            "topic_id"             => $this->topic_id,
            "privacy"              => $this->privacy,
            "subscription_type"    => $this->subscription_type,
            "amount"               => (double)$this->amount,
            "project_instructions" => $this->project_instructions,
            "is_for_kid"           => $this->is_for_kid,
            "name"                 => $this->name,
            "description"          => $this->description,
            "publish_date"         => $this->publish_date,
            "image"                => $this->image,
            "invitation_link"      => $this->invitation_link ?? "",
            "created_at"           => $this->created_at,
            "updated_at"           => $this->updated_at,
        ];
    }

    public function getLastWatchedTime()
    {
        return $this->watch_history->count() > 0
            ? (new WatchHistoryResource($this->watch_history))->getLastWatchTime()
            : "";
    }

    public function getCompleteStatus()
    {
        $totalDuration        = (new VideoResource($this->total_videos))->getTotalDuration();
        $totalWatchedDuration = (new WatchHistoryResource($this->watch_history))->totalWatchedDuration();

        if ($totalWatchedDuration == 0) {
            return 0;
        }

        if ($totalWatchedDuration > $totalDuration || $totalWatchedDuration == $totalDuration) {
            return 100;
        }

        return floor(($totalWatchedDuration * 100) / $totalDuration);
    }
}
