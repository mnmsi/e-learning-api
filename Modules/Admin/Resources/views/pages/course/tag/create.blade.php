@extends('admin::template.base_template', [
    'bcrump' => "Course",
    'bcrumpTitle' => "Add New Tag"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 p-md-5 p-4">
                <div class="card-body px-0 pt-0 pb-2">
                    <form role="form" method="POST" action="{{ route('tag.store') }}">
                        @csrf

                        <div class="row justify-content-center">
                            <div class="col-md-7 col-sm-12">
                                <div class="mb-3">
                                    <label>Title</label>
                                    <span class="text-uppercase text-xs font-weight-bold align-items-center">
                                        <i class="fas fa-info-circle ms-auto text-dark cursor-pointer"
                                               data-bs-toggle="tooltip" data-bs-placement="bottom"
                                               title='Title must be a unique and meaningful'></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           placeholder='Write down like "test_title_tag"' aria-label="Title"
                                           name="title" value="{{ old('title') }}">
                                    @include('admin::components.form_field_alert', ['name' => 'title'])
                                </div>

                                <div class="mb-3">
                                    <label>Value</label>
                                    <input type="text" class="form-control" placeholder="Value" aria-label="Value"
                                           name="value" value="{{ old('value') }}">
                                    @include('admin::components.form_field_alert', ['name' => 'value'])
                                </div>

                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea class="form-control" placeholder="Explanation of this tag" rows="3"
                                              name="description">{{old('description')}}</textarea>
                                    @include('admin::components.form_field_alert', ['name' => 'description'])
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="javascript:void(0);" onclick="history.back()"
                               class="btn btn-sm bg-gradient-secondary">Back</a>
                            <button type="submit" class="btn btn-sm bg-gradient-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
