<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title></title>
  <style>
    body {
      color: black;
    }

    .div-otp {
      margin-top: 30px;
      text-align: center;
    }

    .otp {
      width: 150px;
      background-color: #FFE770;
      padding: 20px;
      text-decoration: none;
      color: black;
      text-align: center;
      border-radius: 5px;
      font-size: 20px;
      letter-spacing: 10px;
      display: inline-block;
    }
  </style>
</head>

<body>

  <p>Hello {{ $data['user']->username }},</p>
  <p>You are receiving this email because we received a password reset request for your account.</p>
  <p>
    {{-- Click the button below to reset your password: --}}
    Plese copy otp below to reset your password
    <br>
    {{-- <a href="{{ $data['otp'] }}"
      style="display: inline-block; padding: 10px 15px; background-color: #3490dc; color: #ffffff; text-decoration: none;">Reset
      Password</a> --}}
  <div class="div-otp">
    <p class="otp">{{ $data['otp'] }}</p>
  </div>
  </p>
  <p>If you did not request a password reset, no further action is required.</p>
  <p>Thank you!</p>

</body>

</html>