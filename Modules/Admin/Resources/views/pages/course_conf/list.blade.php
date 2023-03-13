@extends('admin::template.base_template', [
    'bcrump' => "Course Configuration",
    'bcrumpTitle' => "Course Configuration List"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 px-3">
                    <h6 class="mb-0">Course Configurations</h6>
                </div>
                <form role="form" method="POST" action="{{ route('course.conf.update') }}">
                    @csrf
                    <div class="card-body px-0 pt-0 pb-2">
                        @foreach($conf as $c)
                            <div class="p-3">
                                <ul class="list-group">
                                    <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                        <div class="row w-100">
                                            <div class="col-md-4">
                                                <label class="control-label">Course Configuration</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" placeholder="Value"
                                                       aria-label="Name" required
                                                       aria-describedby="email-addon" name="data[{{$c->title}}]"
                                                       value="{{ old('name', $c->value) }}">
                                                @include('admin::components.form_field_alert', ['name' => 'data.' . $c->title])
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center">
                        <a href="javascript:void(0)" onclick="history.back()"
                           class="btn btn-sm bg-gradient-secondary">Back</a>
                        <button type="submit" class="btn btn-sm bg-gradient-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
