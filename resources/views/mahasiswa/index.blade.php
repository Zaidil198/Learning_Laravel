<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <style>
    @include('partials.style')
    </style>
</head>

<body>
    @include('partials.nav')
    <h1>Data Mahasiswa</h1>

    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        @role('admin')
        <a href="{{ route('mahasiswa.create') }}">Tambah Mahasiswa</a>
        |
        @else
        @endrole
        <a href="{{ route('dosen.index') }}">Data Dosen</a>
        |
        <a href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a>
        |
        <a href="{{ route('ruangan.index') }}">Ruangan</a>
        |
        <a href="{{ route('kelas.index') }}">Kelas</a>
        |
        <a href="{{ route('jadwal-kuliah.index') }}">Jadwal Kuliah</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Angkatan</th>
                <th>Email</th>
                <th>Kelas</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mahasiswa as $mhs)
            <tr>
                <td>{{ $mahasiswa->firstItem() + $loop->index }}</td>
                <td>
                    @if ($mhs->foto)
                    <img src="{{ $mhs->foto_url }}" alt="Foto {{ $mhs->nama }}" class="foto-thumb">
                    @else
                    -
                    @endif
                </td>
                <td>{{ $mhs->nim }}</td>
                <td>{{ $mhs->nama }}</td>
                <td>{{ $mhs->jurusan }}</td>
                <td>{{ $mhs->angkatan }}</td>
                <td>{{ $mhs->email }}</td>
                <td>{{ $mhs->kelas->nama_kelas ?? '-' }}</td>
                <td>
                    @role('admin')
                    <a href="{{ route('mahasiswa.edit', $mhs->id) }}" class="btn">Ubah</a>

                    <form action="{{ route('mahasiswa.destroy', $mhs->id) }}" method="POST" style="display: inline"
                        onsubmit="return confirm('Yakin ingin menghapus data mahasiswa ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                    @else
                    <span>-</span>
                    @endrole
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9">Belum ada data mahasiswa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $mahasiswa->links('pagination.custom') }}
</body>

</html>