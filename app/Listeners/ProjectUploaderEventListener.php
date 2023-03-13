<?php

namespace App\Listeners;

use App\Events\ProjectUploaderEvent;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use App\Repositories\PushNotification\PushNotificationRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class ProjectUploaderEventListener
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
     * @param ProjectUploaderEvent $event
     * @return void
     */
    public function handle(ProjectUploaderEvent $event)
    {
        $project      = $event->project;
        $course       = $this->courseRepo->findData($project->course_id);
        $educatorInfo = $this->userRepo->whereFirst([
            'id'                => $course->educator_id,
            'push_notification' => 1
        ]);

        if (empty($educatorInfo)) {
            return;
        }

        $message = [
            "action"     => "project-upload",
            "tutor_id"   => $educatorInfo->id,
            "tutor_name" => $educatorInfo->name,
            "data"       => [
                "course_id"    => $course->id,
                "course_name"  => $course->name,
                "image"        => $course->urlParseData(),
                "project_id"   => $project->id,
                "learner_id"   => Auth::id(),
                "learner_name" => Auth::user()->name,
            ]
        ];

        $pushNotificationData                = $message;
        $pushNotificationData['learner_ids'] = Auth::id();
        $isCreate                            = $this->notificationRepo->insertData($pushNotificationData);

        $message['image'] = $isCreate->image;
        $data             = json_encode([
            "to"   => $educatorInfo->device_id,
            "data" => $message
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
