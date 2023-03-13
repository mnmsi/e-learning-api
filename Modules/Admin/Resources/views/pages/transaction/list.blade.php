@extends('admin::template.base_template', [
    'bcrump' => "Transaction",
    'bcrumpTitle' => "Transaction List"
])
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Transaction List</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="saTable">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Tutor Name
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Account Name
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Account No
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                    Amount
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
                            @if(count($withdraws) > 0)
                                @foreach($withdraws as $withdraw)
                                    <tr cellData="{{$withdraw->id}}">
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{$withdraw->educator->name}}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{$withdraw->bank_account->account_name}}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{$withdraw->bank_account->account_no}}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{$withdraw->amount}}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if($withdraw->status == 1)
                                                <span class="badge badge-sm bg-gradient-success">Approved</span>
                                            @elseif($withdraw->status == 2)
                                                <span class="badge badge-sm bg-gradient-warning">Rejected</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{$withdraw->created_at->format('d/m/Y')}}
                                        </span>
                                        </td>
                                        <td class="align-middle data-action">
                                            @if($withdraw->status == 0)
                                                @include('admin::components.action', [
                                                    'approve' => route('trans.approve', encrypt($withdraw->id)),
                                                    'reject' => route('trans.reject', encrypt($withdraw->id)),
                                                    'rowId' => $withdraw->id
                                                ])
                                            @endif
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
                        {!! $withdraws->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
