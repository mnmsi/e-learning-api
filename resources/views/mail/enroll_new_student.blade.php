Hello <strong>{{ $name }}</strong>,
<p>
    Welcome to the <strong>{{ config('app.name') }}</strong>. Please reset your password.
</p>
<p>
    Click
    <a href="{{ $restLink }}" target="_blank">Here</a>
    to reset your password
</p>

Regards,<br>
<strong>{{ config('app.name') }}</strong>
