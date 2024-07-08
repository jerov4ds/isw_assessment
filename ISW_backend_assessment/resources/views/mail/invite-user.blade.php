<!DOCTYPE html>
<html>
<head>
    <title>Quibitt app invitation</title>
</head>
<body>
<p>Dear {{ $details['name'] }},</p>
<h1>Invitation to participate in a project on the quibitt app</h1>
<p>We wish to inform you that {{ $details['company_name'] }} invited you to participate in a project as a {{ $details['role'] }} on the Quibitt app.</p>
<p>Please visit <a href="{{ $details['url'] }}">{{ $details['url'] }}</a> and log in using the credentials shown below.</p>
<p>
    Username: <strong>{{ $details['username'] }}</strong><br>
    Password: <strong>{{ $details['password'] }}</strong>
</p>

<p>Regards, <br>Quibitt tech team</p>
</body>
</html>
