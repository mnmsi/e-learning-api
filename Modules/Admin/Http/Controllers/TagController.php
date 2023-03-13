<?php

namespace Modules\Admin\Http\Controllers;

use App\Exceptions\ControllerException;
use App\Repositories\CourseTag\CourseTagRepositoryInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Admin\Http\Requests\TagRequest;

class TagController extends Controller
{
    private $tagRepo;

    public function __construct(CourseTagRepositoryInterface $tagRepo)
    {
        $this->tagRepo = $tagRepo;
    }

    /**
     * Show the form for editing the specified resource.
     * @return Renderable
     */
    public function tags()
    {
        $tags = $this->tagRepo->getData();

        return view('admin::pages.course.tag.edit', compact('tags'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Renderable
     * @throws ControllerException
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->formData as $title => $value) {
                $this->tagRepo->updateOrCreate(['title' => $title], ['value' => $value]);
            }
        }
        catch (\Exception $exception) {
            DB::rollBack();
            throw new ControllerException();
        }

        DB::commit();
        return ControllerException::success();
    }

    public function create()
    {
        return view('admin::pages.course.tag.create');
    }

    public function store(TagRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|unique:App\Models\CourseTag,title',
            'value'       => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($this->tagRepo->insertData($request->all())) {
            return ControllerException::success('tag.conf');
        } else {
            return ControllerException::error();
        }
    }
}
