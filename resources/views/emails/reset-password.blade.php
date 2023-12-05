<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title></title>
</head>

<body>

  <p>Hello {{ $data['user']->username }},</p>
  <p>You are receiving this email because we received a password reset request for your account.</p>
  <p>
    Click the button below to reset your password:
    <br>
    <a href="{{ $data['resetLink'] }}"
      style="display: inline-block; padding: 10px 15px; background-color: #3490dc; color: #ffffff; text-decoration: none;">Reset
      Password</a>
  </p>
  <p>If you did not request a password reset, no further action is required.</p>
  <p>Thank you!</p>

</body>

</html>