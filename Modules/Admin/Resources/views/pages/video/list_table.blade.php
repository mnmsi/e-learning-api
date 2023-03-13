<table class="table align-items-center mb-0">
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
    @if(!empty($videos) && count($videos) > 0)
        @foreach($videos as $video)
            <tr cellData="{{md5($video->id)}}">
                <td>
                    <div class="d-flex px-2 py-1">
                        <div>
                            <img src="{{$video->video_thumbnail}}"
                                 class="avatar avatar-sm me-3"
                                 alt="user1">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{$video->title}}</h6>
                            <p class="text-xs text-secondary mb-0">{{$video->location}}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="text-xs font-weight-bold">
                        {{$video->description}}
                    </span>
                </td>
                <td>
                    <span class="text-xs font-weight-bold">
                        {{$video->duration}}
                    </span>
                </td>
                <td class="align-middle text-center">
                    <span class="text-xs font-weight-bold">
                        {{$video->created_at->format('Y-m-d')}}
                    </span>
                </td>
                <td class="align-middle data-action">
                    @include('admin::components.action', [
                        'edit' => route('video.edit', encrypt($video->id)),
                        'delete' => route('video.delete', encrypt($video->id)),
                        'rowId' => md5($video->id)
                    ])
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="5" class="text-center">
                <span class="text-xs font-weight-bold">
                    No video available
                </span>
            </td>
        </tr>
    @endif
    </tbody>
</table>
