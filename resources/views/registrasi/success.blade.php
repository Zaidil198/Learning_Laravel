<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil</title>
    <style>
    @include('partials.style') body {
        max-width: 640px;
        background-color: #f7f8fa;
    }

    .success-card {
        margin-top: 40px;
        padding: 30px;
        background: #fff;
        border: 1px solid #ddd;
        border-top: 6px solid #2da44e;
        border-radius: 8px;
    }

    .success-card h1 {
        margin-top: 0;
        color: #176b2c;
    }

    .success-card .nomor {
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 1px;
        color: #155eef;
    }

    .success-card table {
        margin-top: 22px;
    }

    .status {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        background: #fff4d6;
        color: #8a6100;
        font-size: 13px;
        font-weight: bold;
    }

    .success-card .btn {
        margin-top: 22px;
    }
    </style>
</head>

<body>
    <div class="success-card">
        <h1>Pendaftaran Berhasil</h1>
        <p>
            Terima kasih, <strong>{{ $hasil['nama_lengkap'] }}</strong>.
            Data pendaftaran Anda sudah kami terima.
        </p>

        <table>
            <tr>
                <th>Nomor Pendaftaran</th>
                <td class="nomor">{{ $hasil['nomor_pendaftaran'] }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $hasil['email'] }}</td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td>{{ $hasil['program_studi'] }} ({{ $hasil['jenjang'] }})</td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="status">Menunggu verifikasi</span></td>
            </tr>
        </table>

        <p class="hint">
            Simpan nomor pendaftaran ini. Akun login sudah dibuat dengan email yang Anda daftarkan.
        </p>

        <a href="{{ route('login') }}" class="btn">Masuk ke Aplikasi</a>
    </div>
</body>

</html>