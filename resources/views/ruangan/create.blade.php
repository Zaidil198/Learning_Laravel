<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ruangan</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Tambah Ruangan</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('ruangan.store') }}" method="POST">
        @csrf

        <p>
            <label for="kode_ruangan">Kode Ruangan</label><br>
            <input type="text" id="kode_ruangan" name="kode_ruangan" value="{{ old('kode_ruangan') }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" maxlength="100" required>
        </p>

        <p>
            <label for="gedung">Gedung</label><br>
            <input type="text" id="gedung" name="gedung" value="{{ old('gedung') }}" maxlength="100" required>
        </p>

        <p>
            <label for="kapasitas">Kapasitas</label><br>
            <input type="number" id="kapasitas" name="kapasitas" value="{{ old('kapasitas') }}" min="1" max="9999" required>
        </p>

        <p>
            <label style="font-weight: normal;">
                <input type="checkbox" name="is_lab" value="1" {{ old('is_lab') ? 'checked' : '' }}>
                Ruangan Lab
            </label>
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('ruangan.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
