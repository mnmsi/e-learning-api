<?php

namespace App\Observers;

use App\Events\CourseCreationEvent;
use App\Models\Course;
use Illuminate\Support\Facades\Gate;

class CourseObserver
{
    /**
     * Handle the Course "created" event.
     *
     * @param Course $course
     * @return void
     */
    public function created(Course $course)
    {
        event(new CourseCreationEvent($course));
    }

    /**
     * Handle the Course "updated" event.
     *
     * @param Course $course
     * @return void
     */
    public function updated(Course $course)
    {
        //
    }

    /**
     * Handle the Course "deleted" event.
     *
     * @param Course $course
     * @return void
     */
    public function deleted(Course $course)
    {
        //
    }

    /**
     * Handle the Course "restored" event.
     *
     * @param Course $course
     * @return void
     */
    public function restored(Course $course)
    {
        //
    }

    /**
     * Handle the Course "force deleted" event.
     *
     * @param Course $course
     * @return void
     */
    public function forceDeleted(Course $course)
    {
        //
    }
}
