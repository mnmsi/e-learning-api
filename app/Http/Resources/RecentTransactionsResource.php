<?php

namespace App\Http\Resources;

use App\Repositories\Course\CourseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RecentTransactionsResource extends JsonResource
{
    private $courseRepo;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->courseRepo = resolve(CourseRepositoryInterface::class);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'date'                => Carbon::parse($this->subscription->created_at)->format('d/m/y'),
            'learner_name'        => $this->learner->name,
            'course_name'         => $this->course->name,
            'actual_amount'       => doubleval($this->course->amount),
            'amount_after_charge' => $this->courseRepo->applyCourseCharge($this->course->course_fee, $this->course->amount),
        ];
    }
}
