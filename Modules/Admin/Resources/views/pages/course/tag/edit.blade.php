@extends('admin::template.base_template', [
    'bcrump' => "Course Tag",
    'bcrumpTitle' => "Add/Edit Tag"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 p-4">
                <div class="card-header px-0">
                    <h6 class="position-absolute">Tag Configurations</h6>
                    <div class="text-end">
                        <a class="btn btn-sm bg-gradient-dark mb-0" href="{{route('tag.create')}}">
                            <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Tag</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-2 pb-2">
                    <form role="form" method="POST" action="{{ route('tag.update') }}"
                          enctype="multipart/form-data">
                        @csrf

                        @foreach($tags as $tag)
                            <div class="row mb-3">
                                <div class="col-3">
                                    <span class="text-uppercase text-xs font-weight-bold align-items-center">
                                        {{Str::replace('_', ' ', $tag->title, )}}
                                        <span class="mx-3">
                                            <i class="fas fa-info-circle ms-auto text-dark cursor-pointer"
                                               data-bs-toggle="tooltip" data-bs-placement="bottom"
                                               title='{{$tag->description ?? $tag->title}}'></i>
                                        </span>
                                    </span>
                                </div>
                                <div class="col-9">
                                    <input type="text" class="form-control"
                                           name="formData[{{$tag->title}}]" value="{{$tag->value}}">
                                </div>
                            </div>
                        @endforeach

                        <div class="text-center mt-3">
                            <a href="javascript:void(0);" onclick="history.back()"
                               class="btn btn-sm bg-gradient-secondary">Back</a>
                            <button type="submit" class="btn btn-sm bg-gradient-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
