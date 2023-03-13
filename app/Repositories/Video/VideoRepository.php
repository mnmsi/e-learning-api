<?php

namespace App\Repositories\Video;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoRepository extends BaseRepository implements VideoRepositoryInterface
{
    protected $courseRepo;

    public function __construct($model, $courseRepo)
    {
        parent::__construct($model);
        $this->courseRepo = $courseRepo;
    }

    public function getAllVideos($filter)
    {
        return $this->courseRepo->getCourseVideos($filter['type'], $filter['course_id']);
    }

    public function uploadVideo($video, $title, $directory)
    {
        $fileExt = $video->extension();
        $video   = file($video);
        $chunks  = array_chunk($video, 3000);
        $path    = 'temp/' . microtime();

        foreach ($chunks as $key => $chunk) {
            $name = "{$path}/tmp{$key}.{$fileExt}";
            Storage::put($name, $chunk);
        }

        $files     = Storage::files($path);
        $wholeFile = $directory . "/" . Auth::id() . "_" . str_replace(" ", "-", $title) . "_" . md5(microtime()) . ".{$fileExt}";

        foreach ($files as $key => $file) {
            Storage::disk('s3')->append($wholeFile, Storage::get($file));
        }

        Storage::deleteDirectory($path);

        return $wholeFile;
    }
}
