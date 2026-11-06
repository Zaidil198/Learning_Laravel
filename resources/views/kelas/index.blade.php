<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas</title>
    <style>
    @include('partials.style')
    </style>
</head>

<body>
    @include('partials.nav')
    <h1>Data Kelas</h1>

    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('kelas.create') }}">Tambah Kelas</a>
        |
        <a href="{{ route('dosen.index') }}">Data Dosen</a>
        |
        <a href="{{ route('mahasiswa.index') }}">Data Mahasiswa</a>
        |
        <a href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a>
        |
        <a href="{{ route('ruangan.index') }}">Ruangan</a>
        |
        <a href="{{ route('jadwal-kuliah.index') }}">Jadwal Kuliah</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kelas</th>
                <th>Angkatan</th>
                <th>Jurusan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelas as $k)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $k->nama_kelas }}</td>
                <td>{{ $k->angkatan }}</td>
                <td>{{ $k->jurusan }}</td>
                <td>
                    <a href="{{ route('kelas.edit', $k->id) }}" class="btn">Ubah</a>

                    <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" style="display: inline"
                        onsubmit="return confirm('Yakin ingin menghapus data kelas ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Belum ada data kelas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>