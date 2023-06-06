@extends('admin::template.base_template', [
    'bcrump' => "Category",
    'bcrumpTitle' => "Category List"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Category List</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="caTable">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Info
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Description
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Status
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Created At
                                </th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $avatar)
                                <tr cellData="{{$avatar->id}}">
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                @if($avatar->image)
                                                    <img src="{{ route('asset', ['data' => $avatar->image])}}"
                                                         loading="lazy"
                                                         class="avatar avatar-sm me-3" alt="avatar">
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{$avatar->name}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{$avatar->description ?? '-'}}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($avatar->is_active)
                                            <span class="badge badge-sm bg-gradient-success">Active</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{$avatar->created_at->format('d/m/Y')}}
                                        </span>
                                    </td>
                                    <td class="align-middle data-action">
                                        @include('admin::components.action', [
                                            'edit' => route('category.edit', encrypt($avatar->id)),
                                            'delete' => route('category.delete', encrypt($avatar->id)),
                                            'publish' => "publish",
                                            'tableId' => 'caTable',
                                            'rowId' => $avatar->id
                                        ])
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {!! $list->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
