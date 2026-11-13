<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kuliah</title>
    <style>
    @include('partials.style') body {
        max-width: 1200px;
    }
    </style>
</head>

<body>
    @include('partials.nav')
    <h1>Jadwal Kuliah</h1>

    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('jadwal-kuliah.create') }}" class="btn">Tambah Jadwal</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kelas</th>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Ruangan</th>
                <th>Hari</th>
                <th>Jam</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($jadwalKuliah as $jk)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @foreach ($jadwal->lampiran as $lamp)
                    <a href="{{ asset('storage/'.$lamp->path) }}" target="_blank">{{ $lamp->nama_asli }}</a><br>
                    @endforeach
                </td>
                <td>{{ $jk->kelas->nama_kelas }}</td>
                <td>{{ $jk->mataKuliah->nama_mata_kuliah }}</td>
                <td>{{ $jk->dosen->nama }}</td>
                <td>{{ $jk->ruangan->nama }} ({{ $jk->ruangan->kode_ruangan }})</td>
                <td>{{ $jk->hari }}</td>
                <td>{{ substr($jk->jam_mulai, 0, 5) }} - {{ substr($jk->jam_selesai, 0, 5) }}</td>
                <td>
                    <a href="{{ route('jadwal-kuliah.edit', $jk->id) }}" class="btn">Ubah</a>

                    <form action="{{ route('jadwal-kuliah.destroy', $jk->id) }}" method="POST" style="display: inline"
                        onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">Belum ada jadwal kuliah.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>