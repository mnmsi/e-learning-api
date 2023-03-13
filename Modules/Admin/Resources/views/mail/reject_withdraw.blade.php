Hello <strong>{{ $name }}</strong>,
<p>
    Your withdraw request is rejected.
</p>

@if(!empty($notes))
    <p>
        Because, {{$notes}}
    </p>
@endif

Regards,<br>
<strong>{{ config('app.name') }}</strong>
