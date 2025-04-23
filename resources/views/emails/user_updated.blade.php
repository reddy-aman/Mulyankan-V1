<!DOCTYPE html>
<html>
<head>
    <title>User Update Notification</title>
</head>
<body>
    <p>Your course enrollment details for <strong>{{ $details['course_number'] }}</strong> were recently updated.</p>

    <p><strong>Name:</strong> {{ $details['name'] }}</p>
    <p><strong>Email:</strong> {{ $details['email'] }}</p>

    @if ($details['role_name'] === 'Student' && !empty($details['sid']))
        <p><strong>Student ID (SID):</strong> {{ $details['sid'] }}</p>
    @endif

    <p>If you think this was a mistake, please contact the course instructor or support.</p>
    <p>Regards,<br>Mulyankan Team</p>

</body>
</html>
