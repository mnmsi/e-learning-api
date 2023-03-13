<?php

namespace Modules\Admin\Http\Controllers;

use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use App\Repositories\CourseTag\CourseTagRepositoryInterface;
use App\Repositories\Ethnicity\EthnicityRepositoryInterface;
use App\Repositories\Topic\TopicRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Video\VideoRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    private $userRepo, $courseRepo, $enrollRepo, $videoRepo, $topicRepo, $ethnicityRepo;

    public function __construct(
        UserRepositoryInterface         $userRepo,
        CourseRepositoryInterface       $courseRepo,
        CourseEnrollRepositoryInterface $enrollRepo,
        VideoRepositoryInterface        $videoRepo,
        TopicRepositoryInterface        $topicRepo,
        EthnicityRepositoryInterface    $ethnicityRepo
    )
    {
        $this->userRepo      = $userRepo;
        $this->courseRepo    = $courseRepo;
        $this->enrollRepo    = $enrollRepo;
        $this->videoRepo     = $videoRepo;
        $this->topicRepo     = $topicRepo;
        $this->ethnicityRepo = $ethnicityRepo;
    }

    public function index()
    {
        $users       = $this->userRepo->getData();
        $ttlEducator = $users->whereIn('acc_type_id', [1, 2])->count();
        $ttlLearner  = $users->whereIn('acc_type_id', [3, 4])->count();
        $ttlUser     = $ttlEducator + $ttlLearner;

        $country = $this->userRepo->getCountryUsers();

        $courses     = $this->courseRepo->getData();
        $ttlCourse   = $courses->count();
        $freeCourses = $courses->where('subscription_type', 'free')->count();
        $paidCourses = $courses->where('subscription_type', 'paid')->count();

        $ttlEnrolled     = $this->enrollRepo->getEnrolled()->count();
        $ttlSubscribed   = $this->enrollRepo->getEnrolled('paid')->count();
        $ttlFreeEnrolled = $this->enrollRepo->getEnrolled('free')->count();

        $ttlVideos       = $this->videoRepo->getData()->count();
        $topicWiseCourse = $this->topicRepo->getTotalCoursesInEachTopic();

        $ttlEthnicity = $this->ethnicityRepo->getData()->count();

        $data = [
            "user-info"         => [
                "title" => "Total User",
                "value" => $ttlUser,
            ],
            [
                "title" => "Total Course",
                "value" => $ttlCourse
            ],
            "subscription-info" => [
                "title" => "Total Subscriptions",
                "value" => $ttlEnrolled,
            ],
            "media-info"        => [
                "title" => "Total Media",
                "value" => $ttlVideos,
            ],
            [
                "title" => "Total Ethnicity",
                "value" => $ttlEthnicity
            ],
            "category-info"     => [
                "title" => "Total Category",
                "value" => count($topicWiseCourse),
            ],
            "country-info"           => [
                "title" => "Total Country",
                "value" => count($country),
            ],
        ];

        $userData         = [
            [
                "title" => "Total Educator",
                "value" => $ttlEducator
            ],
            [
                "title" => "Total Learner",
                "value" => $ttlLearner
            ],
        ];
        $subscriptionData = [
            [
                "title" => "Paid Subscriptions",
                "value" => $ttlSubscribed
            ],
            [
                "title" => "Free Subscriptions",
                "value" => $ttlFreeEnrolled
            ],
        ];
        $mediaData        = [
            [
                "title" => "Total Videos",
                "value" => $ttlVideos
            ],
            [
                "title" => "Total Audios",
                "value" => 0
            ],
        ];
        $categoryData     = $topicWiseCourse;
        $countryData      = $country;

        return view('admin::pages.dashboard', compact('data', 'userData', 'subscriptionData', 'mediaData', 'categoryData', 'countryData'));
    }

    public function config()
    {
        return view('admin::pages.config');
    }
}
