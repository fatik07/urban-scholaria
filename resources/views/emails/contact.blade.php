<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Helpdesk Urban</title>
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
        <h2>Pesan dari Hubungi Kami</h2>
        <p>Nama: {{ $request->nama_lengkap }}</p>
        <p>Email: {{ $request->email }}</p>
        <p>Pesan: {{ $request->pesan }}</p>
    </div>

    <div class="footer">
      <p>If you need further assistance, please contact our help team.</p>
      <p>Thanks, <br> Urban</p>
    </div>
  </div>

</body>

</html>