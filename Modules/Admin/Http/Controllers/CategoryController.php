<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Exceptions\Exceptions;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    private $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepo = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('admin::pages.category.list', [
            'list' => $this->categoryRepo->getData(15, "DESC")
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('admin::pages.category.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $insertData = $request->all();
            $insertData['image'] = $request->image->store('categories');
            $this->categoryRepo->insertData($insertData);
        } catch (\Exception $exception) {
            throw new ControllerException();
        }

        Cache::forget('categories');
        return ControllerException::success("category.list");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('admin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $cat = $this->categoryRepo->findData(decrypt($id));

        $data = [
            'id' => encrypt($cat->id),
            'name' => $cat->name,
            'description' => $cat->description,
            'image' => $cat->image,
            'is_active' => $cat->is_active,
        ];
        return view('admin::pages.category.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $avatar     = $this->categoryRepo->findData(decrypt($id));
        $updateData = $request->all();

        if ($request->hasFile('image')) {
            $updateData['image'] = $request->image->store('category');
            if (Storage::exists($avatar->image)) {
                Storage::delete($avatar->image);
            }
        }

        if ($avatar->update($updateData)) {
            Cache::forget('categories');
            return ControllerException::success('category.list', 'Successfully updated!');
        }

        return ControllerException::error();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $avatar = $this->categoryRepo->findData(decrypt($id));

            if (Storage::exists($avatar->image)) {
                Storage::delete($avatar->image);
            }

            if ($avatar->delete()) {
                Cache::forget('categories');
                return Exceptions::success();
            } else {
                return Exceptions::error();
            }
        }
        catch (\Exception $exception) {
            throw new Exceptions();
        }
    }
}
