<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Kelas</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Ubah Data Kelas</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('kelas.update', $kls->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="nama_kelas">Nama Kelas</label><br>
            <input type="text" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas', $kls->nama_kelas) }}" maxlength="50" required>
        </p>

        <p>
            <label for="angkatan">Angkatan</label><br>
            <input type="number" id="angkatan" name="angkatan" value="{{ old('angkatan', $kls->angkatan) }}" min="1990" max="2200" required>
        </p>

        <p>
            <label for="jurusan">Jurusan</label><br>
            <input type="text" id="jurusan" name="jurusan" value="{{ old('jurusan', $kls->jurusan) }}" maxlength="100" required>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('kelas.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
