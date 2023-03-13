@extends('admin::template.base_template', [
    'bcrump' => "User Configuration",
    'bcrumpTitle' => "User List"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-2">
                    <h6 class="position-absolute">User List</h6>
                    <div class="text-end">
                        <a class="btn btn-sm bg-gradient-dark mb-0" href="{{route('user.create')}}">
                            <i class="fas fa-plus"></i>&nbsp;&nbsp;Add New User</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Info
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Function
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
                            @foreach($users as $user)
                                <tr cellData="{{md5($user->id)}}">
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{$user->avatar}}" class="avatar avatar-sm me-3"
                                                     alt="user1">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{$user->name}}</h6>
                                                <p class="text-xs text-secondary mb-0">{{$user->email}}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{$user->acc_type->name ?? "Child of " . $user->parent_acc->name}}
                                        </p>
                                        <p class="text-xs text-secondary mb-0">
                                            {{$user->acc_type->role->name ?? $user->parent_acc->acc_type->role->name}}
                                        </p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if(!$user->is_baned)
                                            <span class="badge badge-sm bg-gradient-success">Active</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">Banned</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{$user->created_at->format('d/m/Y')}}
                                        </span>
                                    </td>
                                    <td class="align-middle data-action">
                                        @include('admin::components.action', [
                                            'edit' => route('user.edit', encrypt($user->id)),
                                            'delete' => route('user.delete', encrypt($user->id)),
                                            'rowId' => md5($user->id)
                                        ])
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {!! $users->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
