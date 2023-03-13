<?php

namespace App\Jobs;

use App\Exceptions\Exceptions;
use App\Imports\AddStudentImport;
use App\Notifications\SendMailNotification;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use App\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Repositories\SaveCourse\SaveCourseRepositoryInterface;
use App\Repositories\StudentAddJob\StudentAddJobRepositoryInterface;
use App\Repositories\Subscription\SubscriptionRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class StudentAddJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $studentAddJobRepo;
    private $userRepo;
    private $passwordResetRepo;
    private $subscriptionRepo;
    private $enrollRepo;
    private $saveCourseRepo;
    private $courseRepo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userRepo          = resolve(UserRepositoryInterface::class);
        $this->studentAddJobRepo = resolve(StudentAddJobRepositoryInterface::class);
        $this->passwordResetRepo = resolve(PasswordResetRepositoryInterface::class);
        $this->subscriptionRepo  = resolve(SubscriptionRepositoryInterface::class);
        $this->enrollRepo        = resolve(CourseEnrollRepositoryInterface::class);
        $this->saveCourseRepo    = resolve(SaveCourseRepositoryInterface::class);
        $this->courseRepo        = resolve(CourseRepositoryInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exceptions
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $studentAddFiles = $this->studentAddJobRepo->whereGet(['status' => 0]);

            if ($studentAddFiles->count() > 0) {
                $studentAddFiles->load('course');
                foreach ($studentAddFiles as $stdFile) {

                    $exRow = (Excel::toArray(new AddStudentImport, $stdFile->file, 's3'))[0];

                    foreach ($exRow as $row) {

                        if (empty($row[0]) || empty($row[1])) {
                            continue;
                        }

                        $learnerData['name']        = $row[0];
                        $learnerData['email']       = $row[1];
                        $learnerData['acc_type_id'] = 3;
                        $learnerData['password']    = Hash::make(time());

                        $isNewLearner = false;
                        $token        = null;
                        $learner      = $this->userRepo->whereFirst(['email' => $learnerData['email']]);
                        $course       = $this->courseRepo->courseDetails($stdFile->course_id);

                        if ($learner->acc_type_id == 1) {
                            Log::alert("Try to add educator as a learner in a course " . $course->id . " for mail: " . $learner->email);
                            continue;
                        }

                        if (!$learner) {
                            $learnerData['is_acc_type_update'] = 0;
                            $learner                           = $this->userRepo->insertData($learnerData);
                            $isNewLearner                      = true;

                            $token = encrypt($learner->id);
                            $this->passwordResetRepo->insertData([
                                'email'      => $learner->email,
                                'token'      => $token,
                                'created_at' => now()
                            ]);
                        }

                        if (!$this->enrollRepo->whereExists([
                            'course_id'  => $stdFile->course_id,
                            'learner_id' => $learner->id,
                        ])) {

                            if ($course->subscription_type === 'free') {
                                $isSubscribe = $this->subscriptionRepo->insertData([
                                    'status'      => 1,
                                    'description' => "Student added by tutor " . $course->educator_id,
                                ]);

                                $enroll = $this->enrollRepo->insertData([
                                    'course_id'       => $stdFile->course_id,
                                    'learner_id'      => $learner->id,
                                    'subscription_id' => $isSubscribe->id,
                                ]);
                            }

                            $this->saveCourseRepo->saveCourseFromJob(['course_id' => $stdFile->course_id, 'learner_id' => $learner->id, 'save' => 1]);
                            $learner->notify(new SendMailNotification($isNewLearner, $token, $stdFile));
                        }
                    }

                    $stdFile->update(['status' => 1]);
                }
            }
        }
        catch (\Throwable $th) {
            DB::rollBack();
            throw new Exceptions();
        }

        DB::commit();
        return Exceptions::success();
    }
}
