<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentInfoResource;
use App\Models\EmailVerify;
use App\Notifications\EmailVerificationNotification;
use App\Repositories\AccType\AccTypeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\FollowRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\FollowResource;
use App\Http\Resources\AccTypeResource;
use App\Http\Resources\AgeTypeResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\EthnicityResource;
use App\Http\Requests\TopicInterestRequest;
use App\Http\Resources\SampleAvatarResource;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Topic\TopicRepositoryInterface;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\Follow\FollowRepositoryInterface;
use App\Repositories\AgeType\AgeTypeRepositoryInterface;
use App\Repositories\Ethnicity\EthnicityRepositoryInterface;
use App\Repositories\SampleAvatar\SampleAvatarRepositoryInterface;
use App\Repositories\InterestedTopic\InterestedTopicRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $sampleAvatarRepo;
    protected $accTypeRepo;
    protected $ageTypeRepo;
    protected $ethnicityRepo;
    protected $topicRepo;
    protected $followRepo;
    protected $interestTopicRepo;
    protected $userRepo;
    protected $classRepo;

    public function __construct(
        SampleAvatarRepositoryInterface    $sampleAvatarRepo,
        AgeTypeRepositoryInterface         $ageTypeRepo,
        EthnicityRepositoryInterface       $ethnicityRepo,
        TopicRepositoryInterface           $topicRepo,
        FollowRepositoryInterface          $followRepo,
        InterestedTopicRepositoryInterface $interestTopicRepo,
        UserRepositoryInterface            $userRepo,
        CourseRepositoryInterface          $classRepo,
        AccTypeRepositoryInterface         $accTypeRepo
    )
    {
        $this->sampleAvatarRepo = $sampleAvatarRepo;
        $this->ageTypeRepo = $ageTypeRepo;
        $this->ethnicityRepo = $ethnicityRepo;
        $this->topicRepo = $topicRepo;
        $this->followRepo = $followRepo;
        $this->interestTopicRepo = $interestTopicRepo;
        $this->userRepo = $userRepo;
        $this->classRepo = $classRepo;
        $this->accTypeRepo = $accTypeRepo;
    }

    public function sampleAvatar()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => SampleAvatarResource::collection($this->sampleAvatarRepo->getSampleAvatars()),
            ]);

        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function accTypes($role_id)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => AccTypeResource::collection($this->accTypeRepo->getAccTypes($role_id)),
            ]);

        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function ageTypes()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => AgeTypeResource::collection($this->ageTypeRepo->getAgeTypes()),
            ]);

        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function ethnicity()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => EthnicityResource::collection($this->ethnicityRepo->getEthnicity()),
            ]);

        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function topics()
    {
        try {
            return response()->json([
                'status' => true,
                // 'data'   => TopicResource::collection($this->topicRepo->getTopics()),
                'data' => $this->topicRepo->getTopics(),
            ]);

        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function follow(FollowRequest $request)
    {
        try {
            if ($this->followRepo->follow($request->only('educator_id', 'follow'))) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }

        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function followList()
    {
        try {
            if (Gate::allows('learner')) {
                return response()->json([
                    'status' => true,
                    'data' => FollowResource::collection($this->followRepo
                        ->whereGet(['learner_id' => Auth::id()])),
                ]);
            } else {
                return Exceptions::forbidden();
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function addInterest(TopicInterestRequest $request)
    {
        try {
            if ($this->interestTopicRepo->addNewInterest($request->all())) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function updateUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $userUpdateData = $request->all();

            if (count($userUpdateData)) {
                if (isset($request->course)) {
                    if (Gate::allows('educator')) {

                        $classData = $request->course;

                        if ($classData['image']) {
                            $classData['image'] = $request->course['image']->store('course');
                        }

                        $classData['educator_id'] = $user->id;
                        $classData['invitation_link'] = "broadcast link need to work";
                        $this->classRepo->insertData($classData);

                    } else {
                        return Exceptions::error("Unauthorized for creating course!!");
                    }
                }

                if (isset($request->children)) {
                    if (Gate::allows('learner') && is_null($user->user_parent_id)) {
                        foreach ($request->children as $key => $value) {

                            if (!empty($value['avatar'])) {
                                if (!is_string($value['avatar']) && is_file($value['avatar'])) {
                                    if (getimagesize($value['avatar'])) {
                                        $value['avatar'] = $value['avatar']->store('avatar');
                                    } else {
                                        $value['avatar'] = null;
                                    }
                                } elseif (is_string($value['avatar']) && !is_file($value['avatar'])) {
                                    $value['avatar'] = str_replace("%2F", "/", explode("=", $value['avatar'])[1]);
                                } else {
                                    $value['avatar'] = null;
                                }
                            }

                            $value['user_parent_id'] = $user->id;
                            $value['password'] = $user->password;
                            $this->userRepo->insertData($value);
                        }
                    } else {
                        return Exceptions::error("Unauthorized for add children!!");
                    }
                }

                if (isset($request->interested_topic_ids)) {
                    foreach ($request->interested_topic_ids as $index => $topicId) {
                        $interestedTopic['learner_id'] = $user->id;
                        $interestedTopic['topic_id'] = $topicId;
                        $this->interestTopicRepo->firstOrCreate($interestedTopic);
                    }
                }

                if ($request->hasFile('avatar')) {
                    $userUpdateData['avatar'] = $request->avatar->store('avatar');
                } elseif (is_string($request->avatar)) {
                    $userUpdateData['avatar'] = explode("=", $request->avatar)[1];
                    $userUpdateData['avatar'] = str_replace("%2F", "/", $userUpdateData['avatar']);
                }

                if (!empty($user->social_uid) && $user->is_acc_type_update == 0) {
                    $userUpdateData['is_acc_type_update'] = 1;
                }

                if (isset($request->device_id)) {
                    if ($isDeviceExists = $this->userRepo->whereFirst(['device_id' => $request->device_id])) {
                        $isDeviceExists->update(['device_id' => null]);
                    }
                }

                if ($request->has('work_experience')) {
                    if (empty($request->work_experience)) {
                        $userUpdateData['work_experience'] = null;
                    }
                }

                if ($request->has('hobbies')) {
                    if (empty($request->hobbies)) {
                        $userUpdateData['hobbies'] = null;
                    }
                }

                if ($request->has('about_me')) {
                    if (empty($request->about_me)) {
                        $userUpdateData['about_me'] = null;
                    }
                }

                if ($request->has('name')) {
                    if (!empty($request->name)) {
                        $userUpdateData['name'] = $request->name;
                    }
                }

                if ($request->has('birth_date')) {
                    if (!empty($request->birth_date)) {
                        $userUpdateData['birth_date'] = Carbon::parse($request->birth_date)->format('Y-m-d');
                    }
                }

                $isUpdate = $this->userRepo->userUpdate($userUpdateData);

                if ($request->has('educations')) {
                    $educations = json_decode($request->educations, true);
                    $user->educations()->sync($educations);
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'data' => [
                        'user' => $this->userRepo->findData(Auth::id())->load('user_educations:id,user_id,name,year')->loadCount('course'),
                    ],
                ]);
//                return Exceptions::success();
            } else {
                return Exceptions::error("Empty request!");
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw new Exceptions();
        }
    }

    public function userInfo()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => [
                    'user' => Auth::user()->load('user_educations:id,user_id,name,year')->loadCount('course'),
                ],
            ]);
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function userChildren()
    {
        try {
            if (empty(Auth::user()->user_parent_id)) {
                return response()->json([
                    'status' => true,
                    'data' => $this->userRepo->getChildren(),
                ]);
            } else {
                return Exceptions::error("Unauthorized. You're a child user.");
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function checkFollow($educatorId)
    {
        try {
            if (Gate::allows('learner')) {
                return ['status' => $this->followRepo->whereExists([
                    'learner_id' => Auth::id(),
                    'educator_id' => $educatorId,
                ])];
            } else {
                return Exceptions::forbidden();
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function profileInfo($profileId)
    {
        try {
            $profile = $this->userRepo->findData($profileId);

            if (!$profile) {
                return [
                    'status' => false,
                    'errors' => ["User doesn't exists"]
                ];
            }

            $profile->loadCount('course_info');
            return [
                'status' => true,
                'data' => new StudentInfoResource($profile)
            ];
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function sendVerificationNotification(Request $request)
    {
        try {
//            old
//            $request->user()->sendEmailVerificationNotification();

//            new
            $token = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $email_verify = new EmailVerify();
            $email_verify->user_id = Auth::id();
            $email_verify->token = $token;
            $email_verify->expire_at = Carbon::now()->addMinutes(30);
            $email_verify->save();
            Auth::user()->notify(new EmailVerificationNotification($token));
            return Exceptions::success();
        } catch (\Exception $exception) {
            return Exceptions::error();
        }
    }

    public function verificationReq(EmailVerificationRequest $request)
    {
        try {
            $request->fulfill();
            return Exceptions::success();
        } catch (\Exception $exception) {
            return Exceptions::error();
        }
    }

    public function emailTokenVerify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                Exceptions::error($validator->errors()->first());
            }

            $check = EmailVerify::where('user_id', Auth::id())
                ->where('token', $request->token)
                ->where('status', 0)->first();

            if ($check) {
                $expirationDate = Carbon::parse($check->expire_at);

                if (!$expirationDate->isPast()) {
                    $check->status = 1;
                    $check->save();
                    Auth::user()->markEmailAsVerified();
                    return Exceptions::success();
                } else {
                    return Exceptions::error("Token expired.");
                }
            } else {
                return Exceptions::error("Token used or not found.");
            }

        } catch (\Exception $e) {
            return Exceptions::error($e->getMessage(), 500);
        }
    }
}
