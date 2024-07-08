<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
<h1>Password Reset Request</h1>
<p>Hello,</p>
<p>You are receiving this email because we received a password reset request for your account.</p>
<p>To reset your password, click on the following link:</p>
<a href="{{ $link }}" target="_blank">Reset Password</a>
<p>This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required.</p>
<p>If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
<p><a href="{{ $link }}">{{ $link }}</a></p>
<p>Regards,<br>Your Company</p>
</body>
</html>
