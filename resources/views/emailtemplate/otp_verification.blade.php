<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Alert - {{ $details['website'] }}</title>
</head>
<body>

    <p>Hi, {{ $details['name'] }}!</p>
    <p>We noticed that there was an attempt to login to your {{ $details['website'] }} account on a new device. Please enter the following One Time PIN (OTP) in the {{ $details['website'] }} app to login:</p>

    <h2 style="background-color: #f0f0f0; padding: 10px; display: inline-block;">{{ $details['verification_code'] }}</h2>

    <p>This OTP is valid for 5 minutes</p>

    <p>If this wasn't you:</p>
    <p>Your account may have been compromised. Please call {{ $details['website'] }} Customer Service at {{ $details['phone_number'] }} immediately.</p>

    <p>Thank You,</p>
    <p>{{ $details['website']  }}</p>
    <p>Date : {{ $details['currentDate'] }}</p> 

</body>
</html>
