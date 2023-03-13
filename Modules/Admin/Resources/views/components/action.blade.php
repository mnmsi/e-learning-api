@isset($view)
    <a href="{{ $view }}">
        <i class="fa fa-eye"></i>
    </a>
@endisset
@isset($edit)
    <a class="btn btn-link btn-outline-dark text-dark mb-0" href="{{ $edit }}">
        <i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>
        Edit
    </a>
@endisset
@isset($delete)
    <a class="btn btn-link text-danger btn-outline-danger text-gradient mb-0" href="javascript:void(0)"
       onclick="fnDelete('{{$delete}}', '{{$rowId}}')">
        <i class="far fa-trash-alt me-2"></i>
        Delete
    </a>
@endisset
@isset($suspend)
    <a class="btn btn-link text-info btn-outline-info text-gradient mb-0" href="javascript:void(0)"
       onclick="fnSuspend('{{$suspend}}', '{{$rowId}}')">
        <i class="fas fa-ban me-2"></i>
        Suspend
    </a>
@endisset
@isset($approve)
    <a class="btn btn-link text-info btn-outline-info text-gradient mb-0" href="javascript:void(0)"
       onclick="fnTransactionApprove('{{$approve}}')">
        <i class="fa fa-check-circle"></i>
        Approve
    </a>
@endisset
@isset($reject)
    <a class="btn btn-link text-danger btn-outline-danger text-gradient mb-0" href="javascript:void(0)"
       onclick="fnTransactionReject('{{$reject}}')">
        <i class="fa fa-ban" aria-hidden="true"></i>
        Reject
    </a>
@endisset

