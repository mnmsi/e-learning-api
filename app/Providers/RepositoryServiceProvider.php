<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Models\CourseConfiguration;
use App\Models\CourseEnrolledStudent;
use App\Models\CourseTag;
use App\Models\Discussion;
use App\Models\DiscussionLike;
use App\Models\EducatorBalanceWithdraw;
use App\Models\EducatorBankAccount;
use App\Models\Follower;
use App\Models\InterestedTopic;
use App\Models\PasswordReset;
use App\Models\Project;
use App\Models\ProjectLike;
use App\Models\PushNotification;
use App\Models\Review;
use App\Models\SampleAvatar;
use App\Models\SaveCourse;
use App\Models\StudentAddJobScheduler;
use App\Models\Subscription;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserAccType;
use App\Models\UserAgeType;
use App\Models\UserEthnicity;
use App\Models\UserRole;
use App\Models\Video;
use App\Models\WatchHistory;
use App\Repositories\AccType\AccTypeRepository;
use App\Repositories\AccType\AccTypeRepositoryInterface;
use App\Repositories\AgeType\AgeTypeRepository;
use App\Repositories\AgeType\AgeTypeRepositoryInterface;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\CourseAnnouncement\CourseAnnouncementRepository;
use App\Repositories\CourseAnnouncement\CourseAnnouncementRepositoryInterface;
use App\Repositories\CourseConf\CourseConfRepository;
use App\Repositories\CourseConf\CourseConfRepositoryInterface;
use App\Repositories\CourseEnroll\CourseEnrollRepository;
use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseTag\CourseTagRepository;
use App\Repositories\CourseTag\CourseTagRepositoryInterface;
use App\Repositories\DiscussionLike\DiscussionLikeRepository;
use App\Repositories\DiscussionLike\DiscussionLikeRepositoryInterface;
use App\Repositories\Discussion\DiscussionRepository;
use App\Repositories\Discussion\DiscussionRepositoryInterface;
use App\Repositories\Educator\EducatorRepository;
use App\Repositories\Educator\EducatorRepositoryInterface;
use App\Repositories\EducatorBalanceWithdraw\EducatorBalanceWithdrawRepository;
use App\Repositories\EducatorBalanceWithdraw\EducatorBalanceWithdrawRepositoryInterface;
use App\Repositories\EducatorBankAccount\EducatorBankAccountRepository;
use App\Repositories\EducatorBankAccount\EducatorBankAccountRepositoryInterface;
use App\Repositories\Ethnicity\EthnicityRepository;
use App\Repositories\Ethnicity\EthnicityRepositoryInterface;
use App\Repositories\Follow\FollowRepository;
use App\Repositories\Follow\FollowRepositoryInterface;
use App\Repositories\InterestedTopic\InterestedTopicRepository;
use App\Repositories\InterestedTopic\InterestedTopicRepositoryInterface;
use App\Repositories\PasswordReset\PasswordResetRepository;
use App\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Repositories\ProjectLike\ProjectLikeRepository;
use App\Repositories\ProjectLike\ProjectLikeRepositoryInterface;
use App\Repositories\Project\ProjectRepository;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\PushNotification\PushNotificationRepository;
use App\Repositories\PushNotification\PushNotificationRepositoryInterface;
use App\Repositories\Review\ReviewRepository;
use App\Repositories\Review\ReviewRepositoryInterface;
use App\Repositories\SampleAvatar\SampleAvatarRepository;
use App\Repositories\SampleAvatar\SampleAvatarRepositoryInterface;
use App\Repositories\SaveCourse\SaveCourseRepository;
use App\Repositories\SaveCourse\SaveCourseRepositoryInterface;
use App\Repositories\StudentAddJob\StudentAddJobRepository;
use App\Repositories\StudentAddJob\StudentAddJobRepositoryInterface;
use App\Repositories\Subscription\SubscriptionRepository;
use App\Repositories\Subscription\SubscriptionRepositoryInterface;
use App\Repositories\Topic\TopicRepository;
use App\Repositories\Topic\TopicRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRole\UserRoleRepository;
use App\Repositories\UserRole\UserRoleRepositoryInterface;
use App\Repositories\Video\VideoRepository;
use App\Repositories\Video\VideoRepositoryInterface;
use App\Repositories\WatchHistory\WatchHistoryRepository;
use App\Repositories\WatchHistory\WatchHistoryRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserRepositoryInterface::class, function ($App) {
            return new UserRepository(User::class);
        });

        $this->app->singleton(UserRoleRepositoryInterface::class, function ($App) {
            return new UserRoleRepository(UserRole::class);
        });

        $this->app->singleton(EducatorRepositoryInterface::class, function ($App) {
            return new EducatorRepository(
                User::class,
                resolve(CourseRepositoryInterface::class),
                resolve(CourseEnrollRepositoryInterface::class),
                resolve(EducatorBalanceWithdrawRepositoryInterface::class)
            );
        });

        $this->app->singleton(SampleAvatarRepositoryInterface::class, function ($App) {
            return new SampleAvatarRepository(SampleAvatar::class);
        });

        $this->app->singleton(PasswordResetRepositoryInterface::class, function ($App) {
            return new PasswordResetRepository(PasswordReset::class);
        });

        $this->app->singleton(AgeTypeRepositoryInterface::class, function ($App) {
            return new AgeTypeRepository(UserAgeType::class);
        });

        $this->app->singleton(AccTypeRepositoryInterface::class, function ($App) {
            return new AccTypeRepository(UserAccType::class);
        });

        $this->app->singleton(EthnicityRepositoryInterface::class, function ($App) {
            return new EthnicityRepository(UserEthnicity::class);
        });

        $this->app->singleton(TopicRepositoryInterface::class, function ($App) {
            return new TopicRepository(Topic::class);
        });

        $this->app->singleton(CategoryRepositoryInterface::class, function ($App) {
            return new CategoryRepository(Category::class);
        });

        $this->app->singleton(CourseRepositoryInterface::class, function ($App) {
            return new CourseRepository(
                Course::class,
                resolve(CourseEnrollRepositoryInterface::class),
                resolve(SubscriptionRepositoryInterface::class),
                resolve(CourseTagRepositoryInterface::class),
                resolve(CourseConfRepositoryInterface::class)
            );
        });

        $this->app->singleton(FollowRepositoryInterface::class, function ($App) {
            return new FollowRepository(Follower::class);
        });

        $this->app->singleton(ProjectRepositoryInterface::class, function ($App) {
            return new ProjectRepository(
                Project::class,
                resolve(VideoRepositoryInterface::class)
            );
        });

        $this->app->singleton(ProjectLikeRepositoryInterface::class, function ($App) {
            return new ProjectLikeRepository(ProjectLike::class);
        });

        $this->app->singleton(DiscussionRepositoryInterface::class, function ($App) {
            return new DiscussionRepository(Discussion::class);
        });

        $this->app->singleton(DiscussionLikeRepositoryInterface::class, function ($App) {
            return new DiscussionLikeRepository(DiscussionLike::class);
        });

        $this->app->singleton(VideoRepositoryInterface::class, function ($App) {
            return new VideoRepository(
                Video::class,
                resolve(CourseRepositoryInterface::class)
            );
        });

        $this->app->singleton(InterestedTopicRepositoryInterface::class, function ($App) {
            return new InterestedTopicRepository(InterestedTopic::class);
        });

        $this->app->singleton(ReviewRepositoryInterface::class, function ($App) {
            return new ReviewRepository(Review::class);
        });

        $this->app->singleton(SubscriptionRepositoryInterface::class, function ($App) {
            return new SubscriptionRepository(Subscription::class);
        });

        $this->app->singleton(CourseEnrollRepositoryInterface::class, function ($App) {
            return new CourseEnrollRepository(CourseEnrolledStudent::class);
        });

        $this->app->singleton(SaveCourseRepositoryInterface::class, function ($App) {
            return new SaveCourseRepository(SaveCourse::class);
        });

        $this->app->singleton(WatchHistoryRepositoryInterface::class, function ($App) {
            return new WatchHistoryRepository(WatchHistory::class);
        });

        $this->app->singleton(CourseTagRepositoryInterface::class, function ($App) {
            return new CourseTagRepository(CourseTag::class);
        });

        $this->app->singleton(StudentAddJobRepositoryInterface::class, function ($App) {
            return new StudentAddJobRepository(StudentAddJobScheduler::class);
        });

        $this->app->singleton(PushNotificationRepositoryInterface::class, function ($App) {
            return new PushNotificationRepository(PushNotification::class);
        });

        $this->app->singleton(CourseAnnouncementRepositoryInterface::class, function ($App) {
            return new CourseAnnouncementRepository(CourseAnnouncement::class);
        });

        $this->app->singleton(EducatorBalanceWithdrawRepositoryInterface::class, function ($App) {
            return new EducatorBalanceWithdrawRepository(EducatorBalanceWithdraw::class);
        });

        $this->app->singleton(EducatorBankAccountRepositoryInterface::class, function ($App) {
            return new EducatorBankAccountRepository(EducatorBankAccount::class);
        });

        $this->app->singleton(CourseConfRepositoryInterface::class, function ($App) {
            return new CourseConfRepository(CourseConfiguration::class);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
