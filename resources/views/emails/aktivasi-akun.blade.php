{{--
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background-color: #ffffff;
      padding: 20px;
      color: black;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .logo {
      text-align: center;
    }

    .content {
      margin-top: 70px;
      margin-left: 50px;
      margin-right: 50px;
      color: black;
    }

    .verify {
      margin-top: 30px;
    }

    .verify-btn {
      background-color: #FFE770;
      padding: 10px 35px;
      text-decoration: none;
      color: black;
      text-align: center;
      border-radius: 5px;
    }

    .verify-btn:hover {
      background-color: #F5CC00;
      cursor: pointer;
    }

    .footer {
      margin-top: 30px;
      margin-left: 50px;
      margin-right: 50px;
      font-style: italic;
      color: black;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="logo">
      <img
        src="https://lh3.googleusercontent.com/drive-viewer/AK7aPaDyfxIlJStETbMTGVja1cVYG34pavOXNjqDA022elV59nGGbpFFpyRElvM7GmfvLxMo4mJeN9kgZNzjKBpG_q9l1Koi0g=s2560"
        alt="Logo Urban Scholaria" style="display:block;margin-left: auto; margin-right: auto;" width="200"
        height="200">
    </div>

    <div class="content">
      <h3>Welcome Ubanites!</h3>
      <p>You have successfully created your Urban account using register data filling and we've given you the email,
        <b>{{ $data['user']->email }}</b>.
      </p>
      <p>Please verify email to complete your account setup.</p>
      <div class="verify">
        <form method="GET" action="{{ route('activate.account', ['token' => $data['token']]) }}">
          @csrf
          <button class="verify-btn" type="submit">Aktivasi Akun</button>
        </form>
      </div>
    </div>

    <div class="footer">
      <p>If you need further assistance, please contact our help team.</p>
      <p>Thanks, <br> Urban</p>
    </div>
  </div>

</body>

</html> --}}

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Aktivasi Akun Berhasil</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background-color: #ffffff;
      padding: 20px;
      color: black;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .content {
      margin-top: 70px;
      margin-left: 50px;
      margin-right: 50px;
      color: black;
    }

    .footer {
      margin-top: 30px;
      margin-left: 50px;
      margin-right: 50px;
      font-style: italic;
      color: black;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="content">
      <h3>Halo {{ $data['user']->nama_lengkap }}!</h3>
      <p>
        Kami senang memberitahu Anda bahwa akun Anda di Urban Scholaria telah berhasil diaktivasi oleh admin. Anda
        sekarang dapat mengakses seluruh fitur dan layanan kami.
      </p>
      <p>
        Terima kasih atas kesabaran dan kerjasama Anda selama proses aktivasi. Jika Anda memiliki pertanyaan lebih
        lanjut atau mengalami kendala, jangan ragu untuk menghubungi tim dukungan kami.
      </p>
    </div>

    <div class="footer">
      <p>If you need further assistance, please contact our help team.</p>
      <p>Thanks, <br> Urban</p>
    </div>
  </div>

</body>

</html>