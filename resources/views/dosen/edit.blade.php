<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Dosen</title>
    <style>
    @include('partials.style') body {
        max-width: 600px;
    }
    </style>
</head>

<body>
    <h1>Ubah Data Dosen</h1>

    @if ($errors->any())
    <ul class="errors">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <form action="{{ route('dosen.update', $dosen->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="nidn">NIDN</label><br>
            <input type="text" id="nidn" name="nidn" value="{{ old('nidn', $dosen->nidn) }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $dosen->nama) }}" maxlength="100" required>
        </p>

        <p>
            <label for="bidang_keahlian">Bidang Keahlian</label><br>
            <input type="text" id="bidang_keahlian" name="bidang_keahlian"
                value="{{ old('bidang_keahlian', $dosen->bidang_keahlian) }}" maxlength="100" required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email', $dosen->email) }}" maxlength="100">
        </p>

        <p>
            <label for="no_telepon">No. Telepon</label><br>
            <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $dosen->no_telepon) }}"
                maxlength="20">
        </p>

        <p>
            <label for="foto">Foto</label><br>
            @if ($mahasiswa->foto)
            <img src="{{ $mahasiswa->foto_url }}" alt="Foto {{ $mahasiswa->nama }}" class="foto-preview">
            @endif
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
            <span class="hint">Kosongkan jika tidak ingin mengganti foto. Maks. 2 MB (JPG/PNG/WebP).</span>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('dosen.index') }}" class="btn">Batal</a>
    </form>
</body>

</html>