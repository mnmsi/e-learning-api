<?php

namespace App\Observers;

use App\Events\VideoUploadEvent;
use App\Models\Video;
use Illuminate\Support\Facades\Gate;

class VideoUploadObserver
{
    /**
     * Handle the Video "created" event.
     *
     * @param Video $video
     * @return void
     */
    public function created(Video $video)
    {
        event(new VideoUploadEvent($video));
    }

    /**
     * Handle the Video "updated" event.
     *
     * @param Video $video
     * @return void
     */
    public function updated(Video $video)
    {
        //
    }

    /**
     * Handle the Video "deleted" event.
     *
     * @param Video $video
     * @return void
     */
    public function deleted(Video $video)
    {
        //
    }

    /**
     * Handle the Video "restored" event.
     *
     * @param Video $video
     * @return void
     */
    public function restored(Video $video)
    {
        //
    }

    /**
     * Handle the Video "force deleted" event.
     *
     * @param Video $video
     * @return void
     */
    public function forceDeleted(Video $video)
    {
        //
    }
}
