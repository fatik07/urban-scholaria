<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kwitansi dengan nomor registrasi {{ $surat->id}}</title>
    {{--    <title>Kwitansi dengan nomor registrasi {{ $surat->surat->id}}</title>--}}
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial, sans-serif';
            text-align: center;
            /* background-color: #fff; */
            background-color: #191d88;
        }

        .container {
            position: relative;
            height: 100vh;
        }

        .content {
            position: absolute;
            top: 42.5%;
            left: 30%;
            transform: translate(-50%, -50%);
            background-color: #FFF5C2;
            border-radius: 15px;
            padding: 5rem 10rem;
            text-align: center;
        }

        .title {
            font-size: 1.25rem;
            margin-top: 30px;
            font-weight: bold;
        }

        .description {
            font-size: 0.90rem;
            margin-top: 0.5rem;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="content">
        <div>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/img/logo.png'))) }}"
                 height="200" width="200" alt="logo urban scholaria" style="margin-top: -20px">
        </div>

        <div>
            <img src="data:image/png;base64,{{ $qrcode }}" style="margin-top: 30px">
        </div>

        <div>
            <p class="title">Nomor Registrasi</p>
            <p class="description">{{ $surat->id ?? null }}</p>
            {{--        <p class="description">{{ $surat->surat->id }}</p>--}}
        </div>

        <div>
            <p class="title">Nama Surat</p>
            <p class="description">{{ $surat->nama ?? null }}</p>
            {{--        <p class="description">{{ $surat->surat->nama }}</p>--}}
        </div>

        <div>
            <p class="title">Tanggal Pengajuan</p>
            @php
                $tanggal = date("d F Y", strtotime($surat->created_at));
        //        $tanggal = date("d F Y", strtotime($surat->surat->created_at));
            @endphp
            <p class="description">{{ $tanggal ?? null}}</p>
        </div>
        <br><br>
    </div>
</body>

</html>
