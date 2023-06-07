<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verification</title>
</head>
<body style="background-color: #edf2f7; padding: 40px;">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="header" style="text-align: center">
            <a href="{{ \Illuminate\Support\Env::get('APP_URL') }}" style="display: inline-block;">
                <img src="{{asset('assets/img/app_logo.png')}}" style="width: auto !important;" class="logo"
                     alt="Leaning">
            </a>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding: 40px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff;">
                <tr>
                    <td align="center" style="padding: 40px;">
                        <h1>Email Verification Token:</h1>
                        <p>Hello!</p>
                        <p>Please use the following verification token to verify your email address:</p>
                        <p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif,
    'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; font-size: 30px; font-weight: bolder; letter-spacing: 10px;">{{ $token ?? '123456' }}</p>
                        <p>If you did not create an account, no further action is required.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td style="color: #b0adc5;" class="content-cell" align="center">
                        © 2023 {{Illuminate\Support\Env::get('APP_NAME')}}. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
