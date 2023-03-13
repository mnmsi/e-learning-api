@extends('admin::template.base_template', [
    'bcrump' => "Course",
    'bcrumpTitle' => "Edit Course"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 p-md-5 p-4">
                <div class="card-body px-0 pt-0 pb-2">
                    <form role="form" method="POST" action="{{ route('course.update', $id) }}"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <label>Course Name</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Name" aria-label="Name"
                                           aria-describedby="email-addon" name="name"
                                           value="{{ old('name', $name) }}">
                                    @include('admin::components.form_field_alert', ['name' => 'name'])
                                </div>

                                <label>Course Type</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="type" aria-label="type"
                                           name="type" value="{{ old('type', $type) }}">
                                    @include('admin::components.form_field_alert', ['name' => 'type'])
                                </div>

                                <label>Privacy</label>
                                <div class="mb-3">
                                    <select class="form-control" name="privacy">
                                        <option value="public"
                                            {{old('privacy', $privacy) == "public" ? 'selected' : ''}}>Public
                                        </option>
                                        <option value="private"
                                            {{old('privacy', $privacy) == "private" ? 'selected' : ''}}>Private
                                        </option>
                                    </select>
                                    @include('admin::components.form_field_alert', ['name' => 'privacy'])
                                </div>

                                <label>Subscription</label>
                                <div class="mb-3">
                                    <select class="form-control" name="subscription_type" id="subscription_type">
                                        <option value="free"
                                            {{old('subscription_type', $subscription_type) == "free" ? 'selected' : ''}}>
                                            Free
                                        </option>
                                        <option value="paid"
                                            {{old('subscription_type', $subscription_type) == "paid" ? 'selected' : ''}}>
                                            Paid
                                        </option>
                                    </select>
                                    @include('admin::components.form_field_alert', ['name' => 'subscription_type'])
                                </div>

                                <label>Publish Date</label>
                                <div class="mb-3">
                                    <input type="date" class="form-control" name="publish_date"
                                           value="{{old('publish_date',$publish_date)}}" readonly>
                                    @include('admin::components.form_field_alert', ['name' => 'publish_date'])
                                </div>

                                <label>Amount</label>
                                <div class="mb-3">
                                    <input type="number" class="form-control" name="amount" id="amount"
                                           value="{{old('amount', ($subscription_type == "free" ? 0 : $amount))}}" {{$subscription_type == "free" ? "readonly" : ""}}>
                                    @include('admin::components.form_field_alert', ['name' => 'amount'])
                                </div>

                                <label>Description</label>
                                <div class="mb-3">
                                    <textarea class="form-control" placeholder="Description" rows="3"
                                              name="description">{{old('description', $description)}}</textarea>
                                    @include('admin::components.form_field_alert', ['name' => 'description'])
                                </div>

                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Image</label>
                                    <input type="file" class="dropify form-control"
                                           accept="image/*" id="image" data-default-file="{{ $image }}"
                                           name="image"/>
                                    @include('admin::components.form_field_alert', ['name' => 'image'])
                                </div>

                                <label>Project Instruction</label>
                                <div class="mb-3">
                                    <textarea class="form-control" placeholder="Project Instructions"
                                              rows="3"
                                              name="project_instructions">{{old('project_instructions', $project_instructions)}}</textarea>
                                    @include('admin::components.form_field_alert',
                                            ['name' => 'project_instructions'])
                                </div>

                                <label>Educator Name</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control"
                                           value="{{$educator_name}}" readonly>
                                </div>

                                <label>Category</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control"
                                           value="{{$topic_name}}" readonly>
                                </div>

                                <label>Is for Kid?</label>
                                <div class="mb-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="is_for_kid" id="is_for_kid1" value="1"
                                            {{$is_for_kid == 1 ? 'checked' : ''}}>
                                        <label class="form-check-label" for="is_for_kid1">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                               name="is_for_kid" id="is_for_kid2" value="0"
                                            {{$is_for_kid == 0 ? 'checked' : ''}}>
                                        <label class="form-check-label" for="is_for_kid2">No</label>
                                    </div>
                                    @include('admin::components.form_field_alert',
                                            ['name' => 'is_for_kid'])
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-2">
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
@section('script')
    <script>
        $("#subscription_type").change(function () {
            if ($(this).val() === 'paid') {
                $("#amount").attr('readonly', false)
            } else {
                $("#amount").val(0)
                $("#amount").attr('readonly', true)
            }
        })

        @if(old('subscription_type') === 'paid')
            $("#amount").attr('readonly', false)
        @endif
    </script>
@endsection
