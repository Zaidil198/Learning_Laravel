<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Kuliah</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Tambah Mata Kuliah</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('mata-kuliah.store') }}" method="POST">
        @csrf

        <p>
            <label for="kode">Kode</label><br>
            <input type="text" id="kode" name="kode" value="{{ old('kode') }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama_mata_kuliah">Nama Mata Kuliah</label><br>
            <input type="text" id="nama_mata_kuliah" name="nama_mata_kuliah" value="{{ old('nama_mata_kuliah') }}" maxlength="100" required>
        </p>

        <p>
            <label for="sks">SKS</label><br>
            <input type="number" id="sks" name="sks" value="{{ old('sks') }}" min="1" max="10" required>
        </p>

        <p>
            <label for="semester">Semester</label><br>
            <input type="number" id="semester" name="semester" value="{{ old('semester') }}" min="1" max="14" required>
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('mata-kuliah.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
