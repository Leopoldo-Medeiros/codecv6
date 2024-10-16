<!DOCTYPE html>
<html>
<head>
    <title>Verify Email Address</title>
</head>
<body>
<h1>Hello, {{ $user->fullname }}</h1>
<p>Click the link below to verify your email address:</p>
<a href="{{ $verificationUrl }}">Verify Email</a>
</body>
</html>
