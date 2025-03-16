<!DOCTYPE html>
<html>
<head>
    <title>Course Registration Confirmation</title>
</head>
<body>
    <p>Hello {{ $emailData['name'] }},</p>

    <p>Welcome to the course! You have been successfully registered
            in the course <strong>{{ $emailData['course_name'] }}</strong>.
    </p>

    @if ($emailData['registered'])
        <p>We are excited to have you on board and looking forward to your participation.</p>
    @else
        <p>However, you are not yet registered on Mulyankan. Please create an account to access your course materials and updates.</p>
    @endif

    <p>Regards,<br>Mulyankan Team</p>
</body>
</html>
