<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\CourseConfigController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\AuthController;
use Modules\Admin\Http\Controllers\CourseController;
use Modules\Admin\Http\Controllers\SampleAvatarController;
use Modules\Admin\Http\Controllers\TagController;
use Modules\Admin\Http\Controllers\TopicController;
use Modules\Admin\Http\Controllers\TransactionController;
use Modules\Admin\Http\Controllers\UserAccTypeController;
use Modules\Admin\Http\Controllers\UserAgeTypeController;
use Modules\Admin\Http\Controllers\UserController;
use Modules\Admin\Http\Controllers\UserEthnicityController;
use Modules\Admin\Http\Controllers\UserRoleController;
use Modules\Admin\Http\Controllers\VideoController;

Route::prefix('admin')->group(function () {
    Route::get('/', [AuthController::class, 'login']);
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('authenticateUser', [AuthController::class, 'authenticateUser'])->name('auth.user');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('course-configuration', [CourseConfigController::class, 'conf'])->name('course.config');
        Route::post('update', [CourseConfigController::class, 'update'])->name('course.conf.update');

        Route::group(['prefix' => 'user-config', 'as' => 'user.'], function () {
            Route::get('list', [UserController::class, 'users'])->name('list');
            Route::get('create', [UserController::class, 'create'])->name('create');
            Route::post('store', [UserController::class, 'store'])->name('store');
            Route::get('delete/{id}', [UserController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [UserController::class, 'update'])->name('update');
        });

        //Sample Avatars Routes
        Route::group(['prefix' => 'sample-avatar', 'as' => 'sa.'], function () {
            Route::get('list', [SampleAvatarController::class, 'list'])->name('list');
            Route::get('create', [SampleAvatarController::class, 'create'])->name('create');
            Route::post('store', [SampleAvatarController::class, 'store'])->name('store');
            Route::get('delete/{id}', [SampleAvatarController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [SampleAvatarController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [SampleAvatarController::class, 'update'])->name('update');
        });

        //User Role Routes
        Route::group(['prefix' => 'user-role', 'as' => 'ur.'], function () {
            Route::get('list', [UserRoleController::class, 'list'])->name('list');
            Route::get('create', [UserRoleController::class, 'create'])->name('create');
            Route::post('store', [UserRoleController::class, 'store'])->name('store');
            Route::get('delete/{id}', [UserRoleController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [UserRoleController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [UserRoleController::class, 'update'])->name('update');
        });

        //User Acc Type Routes
        Route::group(['prefix' => 'acc-type', 'as' => 'uat.'], function () {
            Route::get('list', [UserAccTypeController::class, 'list'])->name('list');
            Route::get('create', [UserAccTypeController::class, 'create'])->name('create');
            Route::post('store', [UserAccTypeController::class, 'store'])->name('store');
            Route::get('delete/{id}', [UserAccTypeController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [UserAccTypeController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [UserAccTypeController::class, 'update'])->name('update');
        });

        //User Age Type Routes
        Route::group(['prefix' => 'age-type', 'as' => 'uagt.'], function () {
            Route::get('list', [UserAgeTypeController::class, 'list'])->name('list');
            Route::get('create', [UserAgeTypeController::class, 'create'])->name('create');
            Route::post('store', [UserAgeTypeController::class, 'store'])->name('store');
            Route::get('delete/{id}', [UserAgeTypeController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [UserAgeTypeController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [UserAgeTypeController::class, 'update'])->name('update');
        });

        //User Ethnicity Routes
        Route::group(['prefix' => 'user-ethnicity', 'as' => 'ueth.'], function () {
            Route::get('list', [UserEthnicityController::class, 'list'])->name('list');
            Route::get('create', [UserEthnicityController::class, 'create'])->name('create');
            Route::post('store', [UserEthnicityController::class, 'store'])->name('store');
            Route::get('delete/{id}', [UserEthnicityController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [UserEthnicityController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [UserEthnicityController::class, 'update'])->name('update');
        });

        //Topic Routes
        Route::group(['prefix' => 'topic', 'as' => 'topic.'], function () {
            Route::get('list', [TopicController::class, 'list'])->name('list');
            Route::get('create', [TopicController::class, 'create'])->name('create');
            Route::post('store', [TopicController::class, 'store'])->name('store');
            Route::get('delete/{id}', [TopicController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [TopicController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [TopicController::class, 'update'])->name('update');
        });

        //Course Routes
        Route::group(['prefix' => 'course', 'as' => 'course.'], function () {
            Route::get('list', [CourseController::class, 'list'])->name('list');
            Route::get('delete/{id}', [CourseController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [CourseController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [CourseController::class, 'update'])->name('update');
            Route::post('update/{id}', [CourseController::class, 'update'])->name('update');
            Route::get('suspend/{id}', [CourseController::class, 'suspend'])->name('suspend');
        });

        //Videos Routes
        Route::group(['prefix' => 'video', 'as' => 'video.'], function () {
            Route::get('get/{courseId}', [VideoController::class, 'getVideo'])->name('get');
            Route::get('delete/{id}', [VideoController::class, 'delete'])->name('delete');
            Route::get('edit/{id}', [VideoController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [VideoController::class, 'update'])->name('update');
        });

        //Course Tag Routes
        Route::group(['prefix' => 'course-tag', 'as' => 'tag.'], function () {
            Route::get('conf', [TagController::class, 'tags'])->name('conf');
            Route::post('update', [TagController::class, 'update'])->name('update');
            Route::get('create', [TagController::class, 'create'])->name('create');
            Route::post('store', [TagController::class, 'store'])->name('store');
        });

        Route::group(['prefix' => 'transaction', 'as' => 'trans.'], function () {
            Route::get('list', [TransactionController::class, 'transactions'])->name('list');
            Route::put('approve/{id}', [TransactionController::class, 'approve'])->name('approve');
            Route::put('reject/{id}', [TransactionController::class, 'reject'])->name('reject');
        });

        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    });
});
