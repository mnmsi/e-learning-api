Hello <strong>{{ $name }}</strong>,
<p>You're enrolled in <b>{{$course_name}}</b> by <i>{{$educator_name}}</i>.</p>

<p>This is a {{$course_privacy}} course for invites only. Please contact <i>{{$educator_name}}</i> if you require more
    information.</p>

<p>Please click this <a
        href="{{"https://app.tuputime.com/?link=" . urlencode("https://app.tuputime.com/course-share?courseId=" . $course_id) . "&apn=com.iotait.tuputime&ibi=com.tuputime"}}">link</a>
    to access the course from app.</p>

<p>Or, you can access the course through the <a href="{{"https://tuputime.com/course/".$course_id}}">link</a> from web.
</p>

Kind regards,<br>
<strong>TupuTime Team</strong>
