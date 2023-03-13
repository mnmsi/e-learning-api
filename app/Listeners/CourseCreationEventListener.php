<?php

namespace App\Listeners;

use App\Events\CourseCreationEvent;
use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use App\Repositories\Follow\FollowRepositoryInterface;
use App\Repositories\PushNotification\PushNotificationRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseCreationEventListener
{
    private $notificationRepo, $followRepo, $userRepo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        PushNotificationRepositoryInterface $notificationRepo,
        FollowRepositoryInterface           $followRepo,
        UserRepositoryInterface             $userRepo
    )
    {
        $this->notificationRepo = $notificationRepo;
        $this->followRepo       = $followRepo;
        $this->userRepo         = $userRepo;
    }

    /**
     * Handle the event.
     *
     * @param CourseCreationEvent $event
     * @return void
     */
    public function handle(CourseCreationEvent $event)
    {
        $course             = $event->course;
        $getFollowedStudent = $this->followRepo
            ->whereGet(['educator_id' => Auth::id()])
            ->pluck('learner_id')
            ->all();

        $followedUser = $this->userRepo->whereInUser($getFollowedStudent);

        if (empty($followedUser)) {
            return;
        }

        $followedUserId = $followedUser->pluck('id')->all();
        $device_tokens  = $followedUser->pluck('device_id')->all();

        $message = [
            "action"     => "course-create",
            "tutor_id"   => $course->educator_id,
            "tutor_name" => $course->educator->name,
            "data"       => [
                "course_id"   => $course->id,
                "course_name" => $course->name,
                "image"       => $course->urlParseData(),
            ]
        ];

        $pushNotificationData                = $message;
        $pushNotificationData['learner_ids'] = json_encode($followedUserId);
        $isCreate                            = $this->notificationRepo->insertData($pushNotificationData);

        $message['image'] = $isCreate->image;
        $data             = json_encode([
            "registration_ids" => $device_tokens,
            "data"             => $message
        ]);

        $headers = [
            'Authorization: key=' . env('NOTIFICATION_SERVER_KEY'),
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
