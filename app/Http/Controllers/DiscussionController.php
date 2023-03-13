<?php

namespace App\Http\Controllers;

use App\Exceptions\Exceptions;
use App\Http\Requests\DiscussionRequest;
use App\Http\Resources\DiscussionResource;
use App\Http\Requests\DiscussionLikeRequest;
use App\Repositories\Discussion\DiscussionRepositoryInterface;
use App\Repositories\DiscussionLike\DiscussionLikeRepositoryInterface;

class DiscussionController extends Controller
{
    protected $discussionRepo;
    protected $discussionLikeRepo;

    public function __construct(
        DiscussionRepositoryInterface $discussionRepo,
        DiscussionLikeRepositoryInterface $discussionLikeRepo
    ) {
        $this->discussionRepo     = $discussionRepo;
        $this->discussionLikeRepo = $discussionLikeRepo;
    }

    public function discussions($courseId)
    {
        try {
            if ($data = $this->discussionRepo->whereGet([
                'course_id' => $courseId,
                'parent_id' => null,
            ])) {
                $response = [
                    'status' => true,
                    'data'   => DiscussionResource::collection($data),
                ];
            } else {
                $response = [
                    'status' => false,
                    'msg'    => ["Invalid type!"],
                ];
            }

        } catch (\Throwable $th) {
            $response = [
                'status' => false,
                'msg'    => ['Something went wrong!'],
            ];
        }

        return response()->json($response);
    }

    public function addDiscussion(DiscussionRequest $request)
    {
        try {
            if (isset($request->parent_id)) {
                if (!empty($this->discussionRepo->whereGetValue(['id' => $request->parent_id], 'parent_id'))) {
                    return response()->json([
                        'status' => false,
                        'msg'    => ['Invalid parent id!'],
                    ]);
                }
            }

            if ($data = $this->discussionRepo->addDiscussion($request->all())) {
                return response()->json([
                    'status' => true,
                    'data'   => $data,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg'    => ['Something went wrong!'],
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'msg'    => ['Something went wrong!'],
            ]);
        }
    }

    public function likeDiscussion(DiscussionLikeRequest $request)
    {
        try {
            if ($this->discussionLikeRepo->likeDiscussion($request->all())) {
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }

        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

}
