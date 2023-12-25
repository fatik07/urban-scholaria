<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  {{--
  <meta http-equiv="X-UA-Compatible" content="ie=edge"> --}}
  <title>Cetak surat legalitas dengan nomor {{ $surat->id }}</title>
  <style>
    * {
      padding: 0 1rem;
      margin: 0 0.5rem;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      padding: 1cm;
      line-height: 1.6;
    }

    .header {
      text-align: center;
      margin-top: 30px;
      margin-bottom: 50px;
    }

    .content {
      margin-bottom: 20px;
    }

    .title {
      width: 150px;
      text-align: left;
      vertical-align: top;
    }

    .semicolon {
      text-align: left;
      vertical-align: top;
    }

    .desc {
      text-align: justify;
    }

    .row {
      display: flex;
      align-items: flex-start;
    }

    .row .title {
      flex: 0 0 auto;
      width: 150px;
    }

    .row .desc {
      flex: 1;
    }

    .footer {
      margin-top: 20px;
    }

    .ttd {
      display: flex;
      justify-content: space-between;
    }

    .page_break {
      page-break-before: always;
    }

    .table2 {
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
    }

    .table2 th,
    .table2 td {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
    }

    .table2 th {
      background-color: #f2f2f2;
    }

    .checkmark {
      color: green;
      /* Change the color as needed */
      font-size: 18px;
      font-weight: bold;
    }
  </style>
</head>

<body>

  <div class="header">
    <h3>SURAT PERNYATAAN LEGALITAS</h3>
    <P>Nomor: {{ $surat->id }}</P>
  </div>

  <div class="content">
    <p>Yang bertanda tangan dibawah ini :</p>

    <table style="margin-top: 20px; margin-bottom:20px">
      <tr>
        <td class="title">Nama</td>
        <td class="semicolon">:</td>
        <td class="desc">{{ $surat->user->nama_lengkap }}</td>
      </tr>
      <tr>
        <td class="title">Email</td>
        <td class="semicolon">:</td>
        <td class="desc">{{ $surat->user->email }}</td>
      </tr>
      <tr>
        <td class="title">Nama Surat</td>
        <td class="semicolon">:</td>
        <td class="desc">{{ $surat->nama }}</td>
      </tr>
      <tr>
        <td class="title">Kategori</td>
        <td class="semicolon">:</td>
        <td class="desc">{{ $surat->kategori }}</td>
      </tr>
      <tr>
        <td class="title">Alamat Lokasi</td>
        <td class="semicolon">:</td>
        <td>{{ $surat->alamat_lokasi }}</td>
      </tr>
      @php
      $jadwal_survey = date("d F Y", strtotime($surat->jadwal_survey));
      $tanggal_pengajuan = date("d F Y", strtotime($surat->created_at));
      @endphp
      <tr>
        <td>Jadwal Survey</td>
        <td>:</td>
        <td>{{ $jadwal_survey }}</td>
      </tr>
      <tr>
        <td>Tanggal Pengajuan</td>
        <td>:</td>
        <td>{{ $tanggal_pengajuan }}</td>
      </tr>
    </table>

    <p style="text-align: justify">Kami menyadari pentingnya memiliki legalitas yang sah dan mengikuti segala aturan
      yang berlaku. Sebagai bentuk tanggung jawab kami terhadap masyarakat dan lingkungan usaha, kami berkomitmen untuk
      memenuhi segala persyaratan yang diperlukan.</p><br>
    <p style="text-align: justify">Atas perhatian dan kerjasamanya, kami mengucapkan terima kasih.</p>
  </div>

  <div class="footer" style="margin-top: 50px">
    <div style="text-align: right">
      @php
      use Carbon\Carbon;
      echo "Surabaya, " . date("d F Y", strtotime(Carbon::now()));
      @endphp
    </div>
    <div class="ttd" style="text-align: right; margin-top:20px">
      <p>Hormat Kami,</p>
      <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/img/logo.png'))) }}" height="100"
        width="100" alt="logo urban scholaria">
      <p>Kepala Dinas</p>
    </div>
  </div>

</body>

</html>