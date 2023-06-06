@extends('admin::template.base_template', [
    'bcrump' => "Category",
    'bcrumpTitle' => "Add New"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 p-md-5 p-4">
                <div class="card-body px-0 pt-0 pb-2">
                    <form role="form" method="POST" action="{{ route('category.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-7 col-sm-12">
                                <label>Name</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Name" aria-label="Name"
                                           aria-describedby="email-addon" name="name" value="{{ old('name') }}">
                                    @include('admin::components.form_field_alert', ['name' => 'name'])
                                </div>

                                <label>Description</label>
                                <div class="mb-3">
                                    <textarea class="form-control" placeholder="Description" rows="4"
                                              name="description">{{old('description')}}</textarea>
                                    @include('admin::components.form_field_alert', ['name' => 'description'])
                                </div>

                                <label>Status</label>
                                <div class="mb-3">
                                    <select class="form-control" name="is_active">
                                        <option value="1" {{old('is_active') == "1" ? 'selected' : ''}}>Active</option>
                                        <option value="0" {{old('is_active') == "0" ? 'selected' : ''}}>Inactive</option>
                                    </select>
                                    @include('admin::components.form_field_alert', ['name' => 'is_active'])
                                </div>
                            </div>

                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Avatar</label>
                                    <input type="file" class="dropify form-control"
                                           accept="image/*" value="{{ old('image') }}" id="image"
                                           name="image" required/>
                                    @include('admin::components.form_field_alert', ['name' => 'image'])
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="javascript:void(0);" onclick="history.back()" class="btn btn-sm bg-gradient-secondary">Back</a>
                            <button type="submit" class="btn btn-sm bg-gradient-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
