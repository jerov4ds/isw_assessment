<!DOCTYPE html>
<html>
<head>
    <title>Quibitt app reset password</title>
</head>
<body>
<p>Dear {{ $details['name'] }},</p>
<h1>Reset Password Reset</h1>
<p>You recently requested to reset your password on the Quibitt app. </p>
<p>Visit this <a href="{{ $details['url'] }}">link</a> and use the one time pin (OTP) bellow to reset your password</p>
<p>
    <strong>{{ $details['otp'] }}</strong>
</p>
<p>Please note that this OTP expires after <strong>20 minutes</strong>. If you did not make this request kindly disregard this email. Your account is still safe</p>
<p>Regards, <br>Quibitt tech team</p>
</body>
</html>
