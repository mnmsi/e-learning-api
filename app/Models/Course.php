<?php

namespace App\Models;

use App\Repositories\CourseTag\CourseTagRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'educator_id',
        'topic_id',
        'privacy',
        'subscription_type',
        'amount',
        'course_fee',
        'is_for_kid',
        'project_instructions',
        'name',
        'type',
        'description',
        'publish_date',
        'image',
        'invitation_link',
        'status',
    ];

    protected $appends = [
        'tags'
    ];

    protected $with = [
        'educator', 'videos', 'follow', 'enroll', 'reviews', 'total_videos', 'watch_history', 'save_course'
    ];

    protected $withCount = [
        'enroll_student', 'total_videos'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('publish_date', function (Builder $builder) {
            $builder->where(function ($q) {
                if (Gate::allows('learner')) {
                    $q->where('publish_date', '<=', now()->format('Y-m-d'));
                }
            });
        });

        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where(function ($q) {
                if (Gate::allows('learner')) {
                    $q->where('status', 1);
                }
            });
        });

        static::addGlobalScope('privacy', function (Builder $builder) {
            $builder->where(function ($q) {
                if (Gate::allows('learner')) {
                    if (!empty(Auth::user()->acc_type_id)) {
                        $q->where('privacy', 'public');
                    }
                }
            });
        });

        static::addGlobalScope('is_for_kid', function (Builder $builder) {
            $builder->where(function ($q) {
                if (Gate::allows('learner')) {
                    if (!empty(Auth::user()->acc_type_id)) {
                        if (Auth::user()->age_type_id == 1) {
                            $q->where('is_for_kid', 1);
                        }
                    }
                }
            });
        });

        static::addGlobalScope('educator_id', function (Builder $builder) {
            $builder->where(function ($q) {
                if (Gate::allows('educator')) {
                    if (!empty(Auth::user()->acc_type_id)) {
                        $q->where('educator_id', Auth::id());
                    }
                }
            });
        });
    }

    public function getImageAttribute($value)
    {
        if (!empty($value)) {
            return route('asset', ['data' => $value]);
        } else {
            return "";
        }
    }

    public function urlParseData()
    {
        parse_str(parse_url($this->image)['query'], $params);

        return $params['data'];
    }

    public function getTagsAttribute()
    {
        $tagArr  = [Str::upper($this->topic->name)];
        $tagRepo = resolve(CourseTagRepositoryInterface::class);
        $hotPick = $tagRepo->whereFirst(['title' => 'hot_pick']);

        if ($this->enroll_student_count >= $hotPick->value) {
            $tagArr[] = 'HOT PICK';
        }

        return $tagArr;
    }

    public function educator()
    {
        return $this->belongsTo(User::class, 'educator_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'id');
    }

    public function videos()
    {
        return $this->hasMany(Video::class)->where(function ($q) {
                if (Gate::allows('educator')) {
                    $q->where('educator_id', Auth::id());
                }
            })->orderBy('order_no', 'ASC')
            ->orderBy('id', 'ASC');
    }

    public function follow()
    {
        return $this->hasOne(Follower::class, 'educator_id', 'educator_id')->whereLearnerId(Auth::id());
    }

    public function students()
    {
        return $this->hasMany(CourseEnrolledStudent::class, 'course_id', 'id');
    }

    public function enroll()
    {
        return $this->hasOne(CourseEnrolledStudent::class, 'course_id', 'id')->whereLearnerId(Auth::id());
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'course_id', 'id')->without('learner');
    }

    public function enroll_student()
    {
        return $this->hasMany(CourseEnrolledStudent::class, 'course_id', 'id')->without('learner');
    }

    public function total_videos()
    {
        return $this->hasMany(Video::class, 'course_id', 'id');
    }

    public function watch_history()
    {
        return $this->hasMany(WatchHistory::class, 'course_id', 'id')->orderBy('created_at', 'desc');
    }

    public function save_course()
    {
        return $this->hasOne(SaveCourse::class, 'course_id', 'id')
                    ->without('course', 'students')
                    ->whereLearnerId(Auth::id());
    }

    public function announcement()
    {
        return $this->hasMany(CourseAnnouncement::class, 'course_id', 'id');
    }

    public function savedCourses()
    {
        return $this->hasMany(SaveCourse::class);
    }

    public function deleteCourseConstraints()
    {
        $this->watch_history()->delete();
        $this->total_videos()->delete();
        $this->announcement()->delete();
        $this->savedCourses()->delete();
        $this->reviews()->delete();
        $this->students()->delete();
    }

}
