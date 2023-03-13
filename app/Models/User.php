<?php

namespace App\Models;

use App\Repositories\CourseEnroll\CourseEnrollRepositoryInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'acc_type_id',
        'age_type_id',
        'ethnicity_id',
        'user_parent_id',
        'name',
        'email',
        'phone',
        'birth_date',
        'email_verified_at',
        'password',
        'avatar',
        'about_me',
        'work_experience',
        'hobbies',
        'country',
        'child_age',
        'social_uid',
        'device_id',
        'email_notification',
        'push_notification',
        'is_acc_type_update',
        'is_skipped_interest',
        'is_skipped_course_creation',
        'is_baned'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = [
        'acc_type',
        'age_type',
        'ethnicity',
        'interested_topic',
        'parent_acc',
    ];

    protected $withCount = [
        'satisfactions', 'tutors',
    ];

    protected $appends = [
        'students_count'
    ];

    public function getStudentsCountAttribute()
    {
        if (!empty($this->acc_type_id)) {
            if (Gate::allows('educator')) {
                return ((resolve(CourseEnrollRepositoryInterface::class))->getStudents())->count();
            }
        }

        return 0;
    }

    public function getAvatarAttribute($value)
    {
        if (!empty($value)) {
            return route('asset', ['data' => $value]);
        } else {
            return "";
        }
    }

    public function acc_type()
    {
        return $this->belongsTo(UserAccType::class, 'acc_type_id', 'id');
    }

    public function age_type()
    {
        return $this->belongsTo(UserAgeType::class, 'age_type_id', 'id');
    }

    public function ethnicity()
    {
        return $this->belongsTo(UserEthnicity::class, 'ethnicity_id', 'id');
    }

    public function interested_topic()
    {
        return $this->hasMany(InterestedTopic::class, 'learner_id');
    }

    public function parent_acc()
    {
        return $this->belongsTo(User::class, 'user_parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'user_parent_id', 'id')->without('parent_acc');
    }

    public function course()
    {
        return $this->hasMany(Course::class, 'educator_id', 'id');
    }

    public function students()
    {
        return $this->hasManyThrough(
            CourseEnrolledStudent::class,
            Course::class,
            'educator_id',
            'course_id',
            'id',
            'id'
        );
    }

    public function satisfactions()
    {
        return $this->hasManyThrough(
            Review::class,
            Course::class,
            'educator_id',
            'course_id',
            'id',
            'id'
        );
    }

    public function tutors()
    {
        return $this->hasMany(Follower::class, 'learner_id', 'id');
    }

    public function course_info()
    {
        return $this->hasMany(Course::class, 'educator_id', 'id')->withoutGlobalScopes();
    }

    public function bank_accounts()
    {
        return $this->hasMany(EducatorBankAccount::class, 'educator_id', 'id');
    }

    public function courseEnroll()
    {
        return $this->hasMany(CourseEnrolledStudent::class, 'learner_id', 'id');
    }

    public function saveCourses()
    {
        return $this->hasMany(SaveCourse::class, 'learner_id', 'id');
    }

    public function watch_history()
    {
        return $this->hasMany(WatchHistory::class, 'learner_id', 'id');
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'educator_id', 'id');
    }

    public function savedCourses()
    {
        return $this->hasMany(SaveCourse::class, 'learner_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'learner_id', 'id');
    }

    public function educations()
    {
        return $this->belongsToMany(User::class, 'user_educations', 'user_id');
    }

    public function user_educations()
    {
        return $this->hasMany(UserEducation::class, '');
    }
}
