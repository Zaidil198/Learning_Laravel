<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <style>
    @include('partials.style') body {
        max-width: 600px;
    }
    </style>
</head>

<body>
    <h1>Tambah Mahasiswa</h1>

    @if ($errors->any())
    <ul class="errors">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <form action="{{ route('mahasiswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <p>
            <label for="nim">NIM</label><br>
            <input type="text" id="nim" name="nim" value="{{ old('nim') }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" maxlength="100" required>
        </p>

        <p>
            <label for="jurusan">Jurusan</label><br>
            <input type="text" id="jurusan" name="jurusan" value="{{ old('jurusan') }}" maxlength="100" required>
        </p>

        <p>
            <label for="angkatan">Angkatan</label><br>
            <input type="number" id="angkatan" name="angkatan" value="{{ old('angkatan') }}" min="1900" max="2200"
                required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="100">
        </p>

        <p>
            <label for="kelas_id">Kelas</label><br>
            <select id="kelas_id" name="kelas_id">
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->nama_kelas }}
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="foto">Foto</label><br>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
            <span class="hint">Maks. 2 MB (JPG/PNG/WebP). Foto otomatis dikompres ke WebP maksimal 600px.</span>
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('mahasiswa.index') }}" class="btn">Batal</a>
    </form>
</body>

</html>