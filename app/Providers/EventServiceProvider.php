<?php

namespace App\Providers;

use App\Events\AnnouncementEvent;
use App\Events\CourseCreationEvent;
use App\Events\ProjectUploaderEvent;
use App\Events\VideoUploadEvent;
use App\Listeners\AnnouncementEventListener;
use App\Listeners\CourseCreationEventListener;
use App\Listeners\ProjectUploaderEventListener;
use App\Listeners\VideoUploadListener;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Models\Project;
use App\Models\Video;
use App\Observers\CourseAnnouncementObserver;
use App\Observers\CourseObserver;
use App\Observers\ProjectUploaderObserver;
use App\Observers\VideoUploadObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class          => [
            SendEmailVerificationNotification::class,
        ],
        CourseCreationEvent::class => [
            CourseCreationEventListener::class,
        ],
        VideoUploadEvent::class    => [
            VideoUploadListener::class,
        ],
        AnnouncementEvent::class   => [
            AnnouncementEventListener::class,
        ],
        ProjectUploaderEvent::class   => [
            ProjectUploaderEventListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Course::observe(CourseObserver::class);
        CourseAnnouncement::observe(CourseAnnouncementObserver::class);
        Video::observe(VideoUploadObserver::class);
        Project::observe(ProjectUploaderObserver::class);
    }
}
