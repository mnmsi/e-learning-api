<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Requests\AppleLoginRequest;
use App\Http\Requests\AuthenticateUserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\FacebookLoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Models\EmailVerify;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\ResetMailNotification;
use App\Notifications\SendMailNotification;
use App\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Stevebauman\Location\Facades\Location;

class ApiAuthController extends Controller
{
    private $userRepo;
    private $passwordResetRepo;

    public function __construct(UserRepositoryInterface $userRepo, PasswordResetRepositoryInterface $passwordResetRepo)
    {
        $this->userRepo = $userRepo;
        $this->passwordResetRepo = $passwordResetRepo;
    }

    public function unauthenticated()
    {
        return Exceptions::error("Unauthenticated!", 401);
    }

    public function authenticateUser(AuthenticateUserRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $location = Location::get($request->ip());
            if ($location) {
                $user->update(['country' => $location->countryName]);
            }

            $user->tokens()->delete();
            $token = $user->createToken('user-auth');

            return response()->json([
                'status' => true,
                'data' => [
                    'token' => $token->plainTextToken,
                    'user' => $user,
                ],
            ]);

        } else {
            return Exceptions::error("Email or password doesn't match!", 401);
        }
    }

    public function register(RegisterUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $insertData = $request->all();
            $insertData['password'] = Hash::make($request->password);

            if ($request->hasFile('avatar')) {
                $insertData['avatar'] = $request->avatar->store('avatar');
            }

            $location = Location::get($request->ip());
            if ($location) {
                $insertData['country'] = $location->countryName;
            }

            $user = $this->userRepo->insertData($insertData);

            if ($user) {
                DB::commit();
                $user->tokens()->delete();
                Auth::login($user);
                $token = $user->createToken('user-auth');

//                $user->notify(new SendMailNotification());
//                event(new Registered($user));
                $this->mailSennd();

                return response()->json([
                    'status' => true,
                    'data' => [
                        'token' => $token->plainTextToken,
                        'user' => $this->userRepo->findData(Auth::id()),
                    ],
                ]);

            } else {
                return Exceptions::error();
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw new Exceptions();
        }
    }

    public function socialLogin(SocialLoginRequest $request)
    {
        try {
            $client = new GuzzleHttpClient();
            $apiRequest = $client->request('GET', 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $request->gmail_token);
            $userInfo = json_decode($apiRequest->getBody());

            if (!empty($userInfo->sub)) {

                $user = $this->userRepo->whereFirst(['email' => $userInfo->email]);

                if (!$user) {

                    $location = Location::get($request->ip());
                    $user = $this->userRepo->insertData([
                        'name' => $userInfo->name,
                        'email' => $userInfo->email,
                        'password' => Hash::make(time()),
                        'social_uid' => Hash::make($userInfo->sub),
                        'is_acc_type_update' => 0,
                        'country' => $location->countryName ?? null
                    ]);

                    $user->markEmailAsVerified();

                } else {
                    $user->update(['social_uid' => Hash::make($userInfo->sub)]);
                }

                if (Hash::check($userInfo->sub, $user->social_uid)) {
                    $user->tokens()->delete();
                    Auth::login($user);
                    $token = $user->createToken('user-auth');

                    return response()->json([
                        'status' => true,
                        'data' => [
                            'token' => $token->plainTextToken,
                            'user' => $this->userRepo->findData($user->id),
                        ],
                    ]);
                } else {
                    return Exceptions::error('Invalid Token!');
                }

            } else {
                return Exceptions::error('Invalid Token!');
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    /**
     * @throws Exceptions
     */
    public function appleLogin(AppleLoginRequest $request)
    {
        try {
            $tokenParts = explode(".", $request->token);

            if (count($tokenParts) !== 3) {
                return Exceptions::validationError("Invalid token!!");
            }

            $userInfo = json_decode(base64_decode($tokenParts[1]));

            if (!empty($userInfo->email)) {

                $user = $this->userRepo->whereFirst(['email' => $userInfo->email]);

                if (!$user) {

                    $location = Location::get($request->ip());
                    $user = $this->userRepo->insertData([
                        'name' => $request->name,
                        'email' => $userInfo->email,
                        'password' => Hash::make(time()),
                        'social_uid' => Hash::make($request->token),
                        'is_acc_type_update' => 0,
                        'country' => $location->countryName ?? null
                    ]);
                    $user->markEmailAsVerified();

                } else {
                    $user->update(['social_uid' => Hash::make($request->token)]);
                }

                if (Hash::check($request->token, $user->social_uid)) {
                    $user->tokens()->delete();
                    Auth::login($user);
                    $token = $user->createToken('user-auth');

                    return response()->json([
                        'status' => true,
                        'data' => [
                            'token' => $token->plainTextToken,
                            'user' => $this->userRepo->findData($user->id),
                        ],
                    ]);
                } else {
                    return Exceptions::error('Invalid Token!');
                }
            } else {
                return Exceptions::error('Invalid Token!');
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function facebookLogin(FacebookLoginRequest $request)
    {
        try {
            $client = new GuzzleHttpClient();
            $apiRequest = $client->request('GET', "https://graph.facebook.com/me?fields=name,email&access_token=" . $request->access_token);
            $userInfo = json_decode($apiRequest->getBody());

            if (!empty($userInfo->id)) {

                $userMail = $userInfo->id . '@facebook.com';
                $user = $this->userRepo->whereFirst(['email' => $userInfo->email ?? $userMail]);

                if (!$user) {

                    $location = Location::get($request->ip());
                    $user = $this->userRepo->insertData([
                        'name' => $userInfo->name,
                        'email' => $userInfo->email ?? $userMail,
                        'password' => Hash::make(time()),
                        'social_uid' => Hash::make($request->access_token),
                        'is_acc_type_update' => 0,
                        'country' => $location->countryName ?? null
                    ]);
                    $user->markEmailAsVerified();

                } else {
                    $user->update(['social_uid' => Hash::make($request->access_token)]);
                }

                if (Hash::check($request->access_token, $user->social_uid)) {
                    $user->tokens()->delete();
                    Auth::login($user);
                    $token = $user->createToken('user-auth');

                    return response()->json([
                        'status' => true,
                        'data' => [
                            'token' => $token->plainTextToken,
                            'user' => $this->userRepo->findData($user->id),
                        ],
                    ]);
                } else {
                    return Exceptions::error('Invalid Token!');
                }
            } else {
                return Exceptions::error('Invalid Token!');
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            if ($user = $this->userRepo->updatePassword($request->all())) {
                return Exceptions::success("Password has changed.");
            } else {
                return Exceptions::error('Incorrect old password!');
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function logout()
    {
        try {
            if ($user = Auth::user()) {
                $user->tokens()->delete();
                return ['status' => true];
            }
            return Exceptions::error('Session Expired');
        } catch (Throwable $exception) {
            throw new Exceptions();
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            if ($request->isMethod('POST')) {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|exists:App\Models\User,email'
                ]);

                if ($validator->fails()) {
                    return Exceptions::validationError($validator->errors()->all());
                }

                $request['created_at'] = now();
                $user = $this->userRepo->whereFirst(['email' => $request->email]);
                $request['token'] = encrypt($user->id);
                $isResetInsert = $this->passwordResetRepo->insertData(
                    $request->only($this->passwordResetRepo->getFillable())
                );

                if ($isResetInsert) {
                    $user->notify(new ResetMailNotification($request['token']));
                    return Exceptions::success();
                } else {
                    return Exceptions::error();
                }
            }

            if ($request->isMethod('GET')) {
                $validator = Validator::make($request->all(), [
                    'token' => 'required|exists:App\Models\PasswordReset,token',
                    'password' => 'required|min:8',
                    'confirm_password' => 'required_with:password|same:password|min:8'
                ]);

                if ($validator->fails()) {
                    return Exceptions::validationError($validator->errors()->all());
                }

                $passReset = $this->passwordResetRepo->whereFirst(['token' => $request->token]);
                if (!$passReset) {
                    throw new HttpResponseException(
                        Exceptions::validationError("Invalid token!")
                    );
                }

                $current = Carbon::now();
                $createdAt = Carbon::parse($passReset->created_at);
                $user = $this->userRepo->whereFirst(['email' => $passReset->email]);

                if ($createdAt->diffInMinutes($current) >= 5) {
                    return Exceptions::validationError("Token is expired!");
                } elseif (!$user) {
                    return Exceptions::validationError("Invalid token!");
                } elseif ($user->email != $passReset->email) {
                    return Exceptions::validationError("Invalid token!");
                } elseif ($user->id != decrypt($request->token)) {
                    return Exceptions::validationError("Invalid token!");
                }

                if ($this->userRepo->updateData(['id' => $user->id], ['password' => Hash::make($request->password)])) {
                    return Exceptions::success("Password has been changed.");
                } else {
                    return Exceptions::error('Something went wrong!');
                }
            }
        } catch (\Exception $ex) {
            throw new Exceptions();
        }
    }

    public function checkEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:App\Models\User,email'
            ]);

            if ($validator->fails()) {
                return Exceptions::validationError($validator->errors()->all());
            }

            return Exceptions::success("You can register.");

        } catch (\Exception $exception) {
            throw new Exceptions();
        }
    }

//    send verify token
    private function mailSennd()
    {
        $token = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $email_verify = new EmailVerify();
        $email_verify->user_id = Auth::id();
        $email_verify->token = $token;
        $email_verify->expire_at = Carbon::now()->addMinutes(30);
        $email_verify->save();
        Auth::user()->notify(new EmailVerificationNotification($token));
        return 1;
    }
}
