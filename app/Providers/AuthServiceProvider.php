<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\DiscussionLike;
use App\Models\Project;
use App\Models\ProjectLike;
use App\Models\User;
use App\Models\Video;
use App\Policies\CoursePolicy;
use App\Policies\DiscussionLikePolicy;
use App\Policies\ProjectLikePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\UserPolicy;
use App\Policies\VideoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class           => UserPolicy::class,
        Project::class        => ProjectPolicy::class,
        ProjectLike::class    => ProjectLikePolicy::class,
        DiscussionLike::class => DiscussionLikePolicy::class,
        Course::class         => CoursePolicy::class,
        Video::class          => VideoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin', function (User $user) {
            return $user->acc_type->role->id == 3;
        });

        Gate::define('educator', function (User $user) {

            if (!is_null($user->user_parent_id)) {
                return $user->parent_acc->acc_type->role->id == 1;
            }

            return $user->acc_type->role->id == 1;
        });

        Gate::define('learner', function (User $user) {
            if (!is_null($user->user_parent_id)) {
                return $user->parent_acc->acc_type->role->id == 2;
            }

            return $user->acc_type->role->id == 2;
        });

        Gate::define('email_notification', function (User $user) {
            return $user->email_notification == 1;
        });

        Gate::define('push_notification', function (User $user) {
            return $user->push_notification == 1;
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $id   = $notifiable->getKey();
            $hash = sha1($notifiable->getEmailForVerification());

            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id'   => $id,
                    'hash' => $hash,
                ]
            );

            if (request('platform') === 'web') {
                $link = "https://tuputime.com/email-verification?id=" . $id . "&hash=" . $hash;
            } else {
                $link = "https://app.tuputime.com/?link=" . urlencode("https://app.tuputime.com/email-verification?id=" . $id . "&hash=" . $hash) . "&apn=com.iotait.tuputime&ibi=com.tuputime";
            }

            return $link;
        });
    }
}
