@extends('admin::template.base_template', [
    'bcrump' => "User Role",
    'bcrumpTitle' => "Role List"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>User Role List</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="saTable">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Name
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
                            @foreach($roles as $role)
                                <tr cellData="{{$role->id}}">
                                    <td>
                                        <div class="d-flex flex-column justify-content-center mx-3">
                                            <h6 class="mb-0 text-sm">{{$role->name}}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{$role->description}}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($role->is_active)
                                            <span class="badge badge-sm bg-gradient-success">Active</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{$role->created_at->format('d/m/Y')}}
                                        </span>
                                    </td>
                                    <td class="align-middle data-action">
                                        @include('admin::components.action', [
                                            'edit' => route('ur.edit', encrypt($role->id)),
                                            'delete' => route('ur.delete', encrypt($role->id)),
                                            'publish' => "publish",
                                            'tableId' => 'saTable',
                                            'rowId' => $role->id
                                        ])
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {!! $roles->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
