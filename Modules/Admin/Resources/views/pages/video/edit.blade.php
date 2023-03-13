@extends('admin::template.base_template', [
    'bcrump' => "Video",
    'bcrumpTitle' => "Edit Video"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 p-md-5 p-4">
                <div class="card-body px-0 pt-0 pb-2">
                    <form role="form" method="POST" action="{{ route('video.update', $id) }}"
                          enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="course_id" value="{{$course_id}}">

                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <label>Title</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="title"
                                           aria-label="title" name="title"
                                           value="{{ old('title', $title) }}">
                                    @include('admin::components.form_field_alert', ['name' => 'title'])
                                </div>

                                <label>Description</label>
                                <div class="mb-3">
                                    <textarea class="form-control" placeholder="Description" rows="3"
                                              name="description">{{old('description', $description)}}</textarea>
                                    @include('admin::components.form_field_alert', ['name' => 'description'])
                                </div>

                                <label>Location</label>
                                <div class="mb-3">
                                    <input type="text" class="form-control"
                                           placeholder="Location" aria-label="location"
                                           name="location" value="{{ old('location', $location) }}">
                                    @include('admin::components.form_field_alert', ['name' => 'location'])
                                </div>

                                <label>Order</label>
                                <div class="mb-3">
                                    <input type="number" class="form-control" name="order_no"
                                           value="{{old('order_no',$order_no)}}">
                                    @include('admin::components.form_field_alert', ['name' => 'order_no'])
                                </div>

                                <label>Duration</label>
                                <div class="mb-3">
                                    <input type="number" class="form-control" name="duration"
                                           value="{{old('duration',$duration)}}" readonly>
                                    @include('admin::components.form_field_alert', ['name' => 'duration'])
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Video Thumbnail</label>
                                    <div>
                                        <img src="{{ $video_thumbnail }}" height="200px" width="auto">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Video</label>
                                    <video width="100%" height="240" controls>
                                        <source src="{{$video_url}}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
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
