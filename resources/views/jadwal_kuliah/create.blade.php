<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal Kuliah</title>
    <style>
    @include('partials.style') body {
        max-width: 600px;
    }
    </style>
</head>

<body>
    <h1>Tambah Jadwal Kuliah</h1>

    @if ($errors->any())
    <ul class="errors">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <form action="{{ route('jadwal-kuliah.store') }}" method="POST">
        @csrf

        <p>
            <label for="kelas_id">Kelas</label><br>
            <select id="kelas_id" name="kelas_id" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->nama_kelas }}
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="mata_kuliah_id">Mata Kuliah</label><br>
            <select id="mata_kuliah_id" name="mata_kuliah_id" required>
                <option value="">-- Pilih Mata Kuliah --</option>
                @foreach ($mataKuliah as $mk)
                <option value="{{ $mk->id }}" {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                    {{ $mk->kode }} - {{ $mk->nama_mata_kuliah }}
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="dosen_id">Dosen</label><br>
            <select id="dosen_id" name="dosen_id" required>
                <option value="">-- Pilih Dosen --</option>
                @foreach ($dosen as $dsn)
                <option value="{{ $dsn->id }}" {{ old('dosen_id') == $dsn->id ? 'selected' : '' }}>
                    {{ $dsn->nama }}
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="ruangan_id">Ruangan</label><br>
            <select id="ruangan_id" name="ruangan_id" required>
                <option value="">-- Pilih Ruangan --</option>
                @foreach ($ruangan as $r)
                <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                    {{ $r->kode_ruangan }} - {{ $r->nama }} (Kapasitas: {{ $r->kapasitas }})
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="hari">Hari</label><br>
            <select id="hari" name="hari" required>
                <option value="">-- Pilih Hari --</option>
                @foreach ($hari as $h)
                <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }}>
                    {{ $h }}
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="jam_mulai">Jam Mulai</label><br>
            <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
        </p>

        <p>
            <label for="jam_selesai">Jam Selesai</label><br>
            <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
        </p>

        <p>
            <label for="lampiran">Lampiran Tambahan</label><br>
            <input type="file" id="lampiran" name="lampiran[]" multiple>
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('jadwal-kuliah.index') }}" class="btn">Batal</a>
    </form>
</body>

</html>