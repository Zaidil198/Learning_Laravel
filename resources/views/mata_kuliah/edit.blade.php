<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Mata Kuliah</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Ubah Data Mata Kuliah</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('mata-kuliah.update', $mata_kuliah->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="kode">Kode</label><br>
            <input type="text" id="kode" name="kode" value="{{ old('kode', $mata_kuliah->kode) }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama_mata_kuliah">Nama Mata Kuliah</label><br>
            <input type="text" id="nama_mata_kuliah" name="nama_mata_kuliah" value="{{ old('nama_mata_kuliah', $mata_kuliah->nama_mata_kuliah) }}" maxlength="100" required>
        </p>

        <p>
            <label for="sks">SKS</label><br>
            <input type="number" id="sks" name="sks" value="{{ old('sks', $mata_kuliah->sks) }}" min="1" max="10" required>
        </p>

        <p>
            <label for="semester">Semester</label><br>
            <input type="number" id="semester" name="semester" value="{{ old('semester', $mata_kuliah->semester) }}" min="1" max="14" required>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('mata-kuliah.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
