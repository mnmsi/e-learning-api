<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\DataResponseResource;
use App\Http\Resources\ReviewListCollection;
use App\Repositories\Review\ReviewRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected $reviewRepo;

    public function __construct(ReviewRepositoryInterface $reviewRepo)
    {
        $this->reviewRepo = $reviewRepo;
    }

    public function getReviews($courseId)
    {
        try {
            if ($data = $this->reviewRepo->whereGet(['course_id' => $courseId])) {
                return new ReviewListCollection($data);
            } else {
                return Exceptions::error();
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function addReview(ReviewRequest $request)
    {
        try {
            $request['learner_id'] = Auth::id();
            if ($review = $this->reviewRepo->insertData($request->all())) {
                return new DataResponseResource($review);
            } else {
                return Exceptions::error();
            }
        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }
}
