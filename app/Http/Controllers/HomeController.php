<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use App\Repositories\Category\CategoryRepositoryInterface;

class HomeController extends Controller
{
    protected $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function categories()
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => CategoryResource::collection($this->categoryRepo->getCategories()),
            ]);

        } catch (\Throwable $th) {
            throw new Exceptions();
        }
    }

    public function getFile(Request $request)
    {
        return Storage::response($request->data);
    }

    public function getFileByPath($path)
    {
        return Storage::response($path);
    }
}
