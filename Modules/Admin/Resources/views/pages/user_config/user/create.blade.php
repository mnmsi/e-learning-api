@extends('admin::template.base_template', [
    'bcrump' => "User Configuration",
    'bcrumpTitle' => "Add New User"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 p-md-5 p-4">
                <div class="card-body px-0 pt-0 pb-2">
                    <form role="form" method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 col-sm-12">

                                <div class="mb-3">
                                    <label>Acc Type</label>
                                    <select class="form-control" id="acc_type_id"
                                            name="acc_type_id" required>
                                        <option>Select Acc Type</option>
                                        @foreach($accTypes as $acType)
                                            <option
                                                value="{{$acType->id}}"
                                                {{old('acc_type_id') == $acType->id ? 'selected' : ''}}>
                                                {{$acType->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @include('admin::components.form_field_alert', ['name' => 'acc_type_id'])
                                </div>

                                <div class="mb-3">
                                    <label>Age Type</label>
                                    <select class="form-control" name="age_type_id" required>
                                        <option>Select Age Type</option>
                                        @foreach($ageTypes as $aType)
                                            @continue($aType->id == 1)
                                            <option
                                                value="{{$aType->id}}"
                                                {{old('age_type_id') == $aType->id ? 'selected' : ''}}>
                                                {{$aType->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @include('admin::components.form_field_alert', ['name' => 'age_type_id'])
                                </div>

                                <div class="mb-3">
                                    <label>Ethnicity</label>
                                    <select class="form-control" name="ethnicity_id" required>
                                        <option>Select Ethnicity</option>
                                        @foreach($ethnicity as $eth)
                                            <option
                                                value="{{$eth->id}}"
                                                {{old('ethnicity_id') == $eth->id ? 'selected' : ''}}>
                                                {{$eth->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @include('admin::components.form_field_alert', ['name' => 'ethnicity_id'])
                                </div>

                                <div class="mb-3">
                                    <label>Name</label>
                                    <input type="text" class="form-control" placeholder="Name"
                                           name="name" value="{{ old('name') }}" required>
                                    @include('admin::components.form_field_alert', ['name' => 'name'])
                                </div>

                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                           placeholder="Email" value="{{ old('email') }}">
                                    @include('admin::components.form_field_alert', ['name' => 'email'])
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">

                                <div class="mb-3">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                           placeholder="Phone Number" value="{{ old('phone') }}">
                                    @include('admin::components.form_field_alert', ['name' => 'phone'])
                                </div>

                                <div class="mb-3">
                                    <label>Birth Date</label>
                                    <input type="date" name="birth_date" class="form-control"
                                           value="{{ old('birth_date') }}">
                                    @include('admin::components.form_field_alert', ['name' => 'birth_date'])
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Avatar</label>
                                    <input type="file" class="dropify form-control"
                                           accept="image/*" id="avatar"
                                           name="avatar"/>
                                    @include('admin::components.form_field_alert', ['name' => 'avatar'])
                                </div>

                            </div>
                        </div>

                        <div class="text-center mt-3">
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
