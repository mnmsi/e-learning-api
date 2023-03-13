<?php

namespace App\Observers;

use App\Events\AnnouncementEvent;
use App\Models\CourseAnnouncement;
use Illuminate\Support\Facades\Gate;

class CourseAnnouncementObserver
{
    /**
     * Handle the CourseAnnouncement "created" event.
     *
     * @param CourseAnnouncement $courseAnnouncement
     * @return void
     */
    public function created(CourseAnnouncement $courseAnnouncement)
    {
        event(new AnnouncementEvent($courseAnnouncement));
    }

    /**
     * Handle the CourseAnnouncement "updated" event.
     *
     * @param CourseAnnouncement $courseAnnouncement
     * @return void
     */
    public function updated(CourseAnnouncement $courseAnnouncement)
    {
        //
    }

    /**
     * Handle the CourseAnnouncement "deleted" event.
     *
     * @param CourseAnnouncement $courseAnnouncement
     * @return void
     */
    public function deleted(CourseAnnouncement $courseAnnouncement)
    {
        //
    }

    /**
     * Handle the CourseAnnouncement "restored" event.
     *
     * @param CourseAnnouncement $courseAnnouncement
     * @return void
     */
    public function restored(CourseAnnouncement $courseAnnouncement)
    {
        //
    }

    /**
     * Handle the CourseAnnouncement "force deleted" event.
     *
     * @param CourseAnnouncement $courseAnnouncement
     * @return void
     */
    public function forceDeleted(CourseAnnouncement $courseAnnouncement)
    {
        //
    }
}
