<?php

namespace App\Http\Controllers;

use App\Events\AnnouncementEvent;
use App\Events\CourseCreationEvent;
use App\Exceptions\Exceptions;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Repositories\PushNotification\PushNotificationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    private $notificationRepo;

    public function __construct(PushNotificationRepositoryInterface $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    public function history()
    {
        try {
            return $this->notificationRepo->getNotification();
        }
        catch (\Exception $ex) {
            throw new Exceptions();
        }
    }

    public function seen($notificationId)
    {
        try {
            if (!$this->notificationRepo->whereExists([
                'id'      => $notificationId,
                'is_seen' => 1
            ])) {
                return $this->notificationRepo->findUpdate($notificationId, ['is_seen' => 1]);
            } else {
                return Exceptions::error("Already seen!", 422);
            }
        }
        catch (\Exception $ex) {
            throw new Exceptions();
        }
    }

    //For testing function
    public function send()
    {
        try {
            return event(new AnnouncementEvent(CourseAnnouncement::where('course_id', 37)->first()));
//            return event(new CourseCreationEvent(Course::where('educator_id', Auth::id())->first()));
        }
        catch (\Exception $exception) {
            return false;
        }
    }
}
