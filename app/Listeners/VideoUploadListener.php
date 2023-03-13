<?php

namespace App\Listeners;

use App\Events\VideoUploadEvent;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use App\Repositories\PushNotification\PushNotificationRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class VideoUploadListener
{
    private $notificationRepo, $enrolledRepo, $userRepo, $courseRepo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        PushNotificationRepositoryInterface $notificationRepo,
        CourseEnrollRepositoryInterface     $enrolledRepo,
        UserRepositoryInterface             $userRepo,
        CourseRepositoryInterface           $courseRepo
    )
    {
        $this->notificationRepo = $notificationRepo;
        $this->enrolledRepo     = $enrolledRepo;
        $this->userRepo         = $userRepo;
        $this->courseRepo       = $courseRepo;
    }

    /**
     * Handle the event.
     *
     * @param VideoUploadEvent $event
     * @return void
     */
    public function handle(VideoUploadEvent $event)
    {
        $video              = $event->video;
        $getEnrolledStudent = $this->enrolledRepo
            ->whereGet(['course_id' => $video->course_id])
            ->pluck('learner_id')
            ->all();

        $course         = $this->courseRepo->findData($video->course_id);
        $enrolledUser   = $this->userRepo->whereInUser($getEnrolledStudent);

        if (empty($enrolledUser)) {
            return;
        }

        $enrolledUserId = $enrolledUser->pluck('id')->all();
        $device_tokens = $enrolledUser->pluck('device_id')->all();

        $message       = [
            "action"     => "video-upload",
            "tutor_id"   => $course->educator_id,
            "tutor_name" => $course->educator->name,
            "data"       => [
                "course_id"   => $course->id,
                "course_name" => $course->name,
                "image"       => $course->urlParseData(),
            ]
        ];

        $pushNotificationData                = $message;
        $pushNotificationData['learner_ids'] = $enrolledUserId;
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
