<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\EducatorAccountController;
use App\Http\Controllers\EducatorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('unauthenticated', [ApiAuthController::class, 'unauthenticated'])->name('unauthenticated');
Route::match(['get', 'post'], 'login', [ApiAuthController::class, 'authenticateUser']);
Route::post('register', [ApiAuthController::class, 'register']);
Route::post('social-login', [ApiAuthController::class, 'socialLogin']);
Route::post('apple-login', [ApiAuthController::class, 'appleLogin']);
Route::post('facebook-login', [ApiAuthController::class, 'facebookLogin']);
Route::get('check-email', [ApiAuthController::class, 'checkEmail']);
Route::match(['get', 'post'], 'reset-password', [ApiAuthController::class, 'resetPassword']);

Route::get('account-types/{role_id}', [UserController::class, 'accTypes']);
Route::get('age-types', [UserController::class, 'ageTypes']);
Route::get('ethnicity', [UserController::class, 'ethnicity']);
Route::get('sample-avatar', [UserController::class, 'sampleAvatar']);
Route::get('topics', [UserController::class, 'topics']);

Route::group(['middleware' => 'guest'], function () {
    Route::get('course-get', [CourseController::class, 'getCourse']);
    Route::get('best-seller-courses', [CourseController::class, 'getBestSellerCourses']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('categories', [HomeController::class, 'categories']);
    Route::post('change-password', [ApiAuthController::class, 'changePassword']);

    Route::group(['prefix' => 'educator', 'middleware' => 'educator'], function () {
        Route::get('student/list', [EducatorController::class, 'studentList']);
//        Route::get('info/{learnerId}', [EducatorController::class, 'studentInfo']);
        Route::get('balance-info', [EducatorController::class, 'balanceInfo']);
        Route::get('transactions', [EducatorController::class, 'transactions']);

        Route::prefix('withdraw')->group(function () {
            Route::get('list', [EducatorController::class, 'myWithdraws']);
            Route::post('request', [EducatorController::class, 'withdrawRequest']);
        });

        Route::prefix('account')->group(function () {
            Route::get('list', [EducatorAccountController::class, 'list']);
            Route::post('add', [EducatorAccountController::class, 'addAccount']);
            Route::get('details/{id}', [EducatorAccountController::class, 'accountDetails']);
            Route::put('update', [EducatorAccountController::class, 'updateAccount']);
            Route::delete('delete/{id}', [EducatorAccountController::class, 'deleteAccount']);
        });
    });

    Route::group(['prefix' => 'learner', 'middleware' => 'learner'], function () {
        Route::get('home', [LearnerController::class, 'getLearnerCourseData']);
    });

    Route::prefix('user')->group(function () {
        Route::get('profile/{profileId}', [UserController::class, 'profileInfo']);
        Route::get('info', [UserController::class, 'userInfo']);
        Route::get('children', [UserController::class, 'userChildren']);
        Route::post('update', [UserController::class, 'updateUser']);

        Route::prefix('email')->group(function () {
            Route::get('verify', [UserController::class, 'sendVerificationNotification']);
            Route::get('verify/{id}/{hash}', [UserController::class, 'verificationReq'])->name('verification.verify');
        });
    });

    Route::prefix('course')->group(function () {
        Route::get('get', [CourseController::class, 'getCourse']);
        Route::get('all', [CourseController::class, 'getEducatorAllCourse']);
        Route::get('by-topic/{topic}', [CourseController::class, 'getCourseByTopic']);
        Route::get('details/{courseId}', [CourseController::class, 'getCourseDetails']);
        Route::get('info/{courseId}', [CourseController::class, 'getCourseDetailsForUpdate']);
        Route::post('create', [CourseController::class, 'createCourse']);
        Route::put('update', [CourseController::class, 'updateCourse']);
        Route::delete('delete/{id}', [CourseController::class, 'deleteCourse']);
        Route::get('search', [CourseController::class, 'searchCourse']);
        Route::get('search-recommendation', [CourseController::class, 'searchRecommendation']);

        Route::get('related/{topicId}', [CourseController::class, 'getRelatedCourse']);
        //For web
        Route::get('learn-next', [CourseController::class, 'getLearnNext']);
        Route::get('check-name', [CourseController::class, 'checkName']);

        Route::post('add-student', [CourseController::class, 'addStudent']);

        Route::prefix('enroll')->group(function () {
            Route::post('/', [CourseController::class, 'enrollCourse']);
            Route::get('list', [CourseController::class, 'getEnrollCourses']);
            Route::delete('remove/{learnerId}', [CourseController::class, 'removeLearner']);
        });

        Route::prefix('video')->group(function () {
            Route::get('all/{course}', [VideoController::class, 'getAllVideosByCourse']);
            Route::get('get', [VideoController::class, 'getAllVideos']);
            Route::post('create', [VideoController::class, 'createVideo']);
            Route::put('update', [VideoController::class, 'updateVideo']);
            Route::delete('delete/{video}', [VideoController::class, 'deleteVideo']);
            Route::post('order', [VideoController::class, 'orderingVideo']);

            Route::get('continue-watching', [VideoController::class, 'continueWatching']);
            Route::prefix('watch-history')->group(function () {
                Route::get('/', [VideoController::class, 'getWatchedList']);
                Route::post('add', [VideoController::class, 'addWatchHistory']);
            });
        });

        Route::prefix('review')->group(function () {
            Route::get('get/{courseId}', [ReviewController::class, 'getReviews']);
            Route::post('add', [ReviewController::class, 'addReview']);
        });

        Route::prefix('save')->group(function () {
            Route::post('/', [CourseController::class, 'saveCourse']);
            Route::get('list', [CourseController::class, 'saveCourseList']);
        });

        Route::prefix('announcement')->group(function () {
            Route::get('all', [AnnouncementController::class, 'getAll']);
            Route::get('get/{courseId}', [AnnouncementController::class, 'getAnnouncement']);
            Route::post('create', [AnnouncementController::class, 'create']);
            Route::delete('delete/{id}', [AnnouncementController::class, 'delete']);
        });
    });

    Route::prefix('project')->group(function () {
        Route::get('get', [ProjectController::class, 'projects']);
        Route::post('create', [ProjectController::class, 'createProject']);
        Route::post('like', [ProjectController::class, 'likeProject']);
        Route::delete('delete/{project}', [ProjectController::class, 'deleteProject']);
    });

    Route::prefix('discussion')->group(function () {
        Route::get('all/{courseId}', [DiscussionController::class, 'discussions']);
        Route::post('add', [DiscussionController::class, 'addDiscussion']);
        Route::post('like', [DiscussionController::class, 'likeDiscussion']);
    });

    Route::prefix('follow')->group(function () {
        Route::post('/', [UserController::class, 'follow']);
        Route::get('list', [UserController::class, 'followList']);
        Route::get('check/{educatorId}', [UserController::class, 'checkFollow']);
    });

    Route::prefix('topic')->group(function () {
        Route::post('interest', [UserController::class, 'addInterest']);
    });

    Route::prefix('notification')->group(function () {
        Route::get('history', [NotificationController::class, 'history']);
        Route::get('seen/{id}', [NotificationController::class, 'seen']);
        Route::get('send', [NotificationController::class, 'send']);
    });

    Route::get('logout', [ApiAuthController::class, 'logout']);
});
