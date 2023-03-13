@extends('admin::template.base_template', [
    'bcrump' => "Course",
    'bcrumpTitle' => "Course List"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Course List</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="saTable">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Educator Info
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Course Info
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Functions
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Total Videos
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Enrolled Students
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Status
                                </th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($courses) > 0)
                                @foreach($courses as $key => $course)
                                    <tr cellData="{{md5($course->id)}}">
                                        <td>
                                            <div class="d-flex px-2 py-1 mwp-150 text-truncate">
                                                <div>
                                                    <img src="{{$course->educator->avatar}}"
                                                         class="avatar avatar-sm me-3" alt="user1">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{$course->educator->name}}</h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{$course->educator->email}}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1 mwp-150 text-truncate">
                                                <div>
                                                    <img src="{{$course->image}}" class="avatar avatar-sm me-3"
                                                         alt="user1">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{$course->name}}</h6>
                                                    <p class="text-xs text-secondary mb-0 text-truncate">
                                                        {{$course->description}}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                <strong>Category</strong>: {{$course->topic->name}}
                                            </p>
                                            <p class="text-xs font-weight-bold mb-0">
                                                <strong>Privacy</strong>: {{$course->privacy}}
                                            </p>
                                            <p class="text-xs font-weight-bold mb-0">
                                                <strong>Subscription</strong>: {{$course->subscription_type}}
                                            </p>
                                            <p class="text-xs font-weight-bold mb-0">
                                                <strong>Amount</strong>: {{$course->amount}}
                                            </p>
                                            <p class="text-xs font-weight-bold mb-0">
                                                <strong>Publish Date</strong>: {{$course->publish_date}}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold">
                                            <a href="javascript:void(0)"
                                               onclick="toggleTableRow(this, {{$key}}, '{{encrypt($course->id)}}')">
                                                <span>{{$course->total_videos_count}}</span>
                                                <i class="fas fa-arrow-down ms-auto text-dark cursor-pointer"
                                                   data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                   title='{{$course->total_videos_count == 0
                                                    ? 'No videos in this course'
                                                    : 'Show Videos'}}'></i>
                                            </a>
                                        </span>
                                        </td>
                                        <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold">
                                            {{$course->enroll_student_count}}
                                        </span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if($course->status == 1)
                                                <span class="badge badge-sm bg-gradient-success">Active</span>
                                            @elseif($course->status == 2)
                                                <span class="badge badge-sm bg-gradient-warning">Suspend</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="align-middle data-action">
                                            @php
                                                $actions = [
                                                    'edit' => route('course.edit', encrypt($course->id)),
                                                    'delete' => route('course.delete', encrypt($course->id)),
                                                    'rowId' => md5($course->id),
                                                ];

                                                if ($course->status != 2) {
                                                    $actions['suspend'] = route('course.suspend', encrypt($course->id));
                                                }
                                            @endphp
                                            @include('admin::components.action', $actions)
                                        </td>
                                    </tr>
                                    <tr class="expandable-row" style="display: none">
                                        <td colspan="7">
                                            <div id="expandable_table_{{$key}}">

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center text-xs font-weight-bold mb-0">
                                        No data found
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {!! $courses->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleTableRow(that, key, courseId) {

            let table = `<table class="table align-items-center mb-0">
<thead>
<tr>
    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
        Video
    </th>
    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
        description
    </th>
    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
        duration
    </th>
    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
        Created At
    </th>
    <th class="text-secondary opacity-7"></th>
</tr>
</thead>
<tbody>
    <tr>
        <td colspan="5" class="text-center">
            <span class="text-xs font-weight-bold">
                No video available
            </span>
        </td>
    </tr>
</tbody>
</table>`

            let $target = $(that);
            let $favIcon = $target.children('i');

            if ($favIcon.hasClass('fa-arrow-down')) {
                $favIcon.removeClass('fa-arrow-down')
                $favIcon.addClass('fa-arrow-up')
            } else {
                $favIcon.addClass('fa-arrow-down')
                $favIcon.removeClass('fa-arrow-up')
            }

            if ($target.closest("td").attr("colspan") > 1) {
                $target.slideUp();
            } else {
                let expTable = $("#expandable_table_" + key);
                if ($(that).children().first().text() == 0) {
                    expTable.children().remove()
                    expTable.append(table)
                    $target.closest("tr").next().toggle('slow');
                    // $target.closest("tr").next().slideToggle();
                } else {
                    expTable.load('{{url('admin/video/get')}}/' + courseId, function (data, status) {
                        if (status === "success") {
                            $target.closest("tr").next().toggle('slow');
                        }
                        if (status === "error") {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: "Something went wrong! Please try again.",
                            });
                        }
                    });
                }
            }
        }
    </script>
@endsection
