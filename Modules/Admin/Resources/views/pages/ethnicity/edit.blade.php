@extends('admin::template.base_template', [
    'bcrump' => "User Ethnicity",
    'bcrumpTitle' => "Edit Ethnicity"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 p-md-5 p-4">
                <div class="card-body px-0 pt-0 pb-2">
                    <form role="form" method="POST" action="{{ route('ueth.update', $id) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row justify-content-center">
                            <div class="col-md-7 col-sm-12">
                                <label>Name</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Name" aria-label="Name"
                                           aria-describedby="email-addon" name="name" value="{{ old('name', $name) }}">
                                    @include('admin::components.form_field_alert', ['name' => 'name'])
                                </div>

                                <label>Description</label>
                                <div class="mb-3">
                                    <textarea class="form-control" placeholder="Description" rows="3"
                                              name="description">{{old('description', $description)}}</textarea>
                                    @include('admin::components.form_field_alert', ['name' => 'description'])
                                </div>

                                <label>Status</label>
                                <div class="mb-3">
                                    <select class="form-control" name="is_active">
                                        <option value="1" {{old('is_active', $is_active) == 1 ? 'selected' : ''}}>Active</option>
                                        <option value="0" {{old('is_active', $is_active) == 0 ? 'selected' : ''}}>Inactive</option>
                                    </select>
                                    @include('admin::components.form_field_alert', ['name' => 'is_active'])
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="javascript:void(0);" onclick="history.back()" class="btn btn-sm bg-gradient-secondary">Back</a>
                            <button type="submit" class="btn btn-sm bg-gradient-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
