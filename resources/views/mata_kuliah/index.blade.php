<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mata Kuliah</title>
    <style>
    @include('partials.style')
    </style>
</head>

<body>
    @include('partials.nav')
    <h1>Data Mata Kuliah</h1>

    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('mata-kuliah.create') }}" class="btn">Tambah Mata Kuliah</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Mata Kuliah</th>
                <th>SKS</th>
                <th>Semester</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mataKuliah as $mk)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if ($mk->modul)
                    <a href="{{ $mk->modul_url }}" target="_blank">Unduh Modul</a>
                    @else
                    -
                    @endif
                </td>
                <td>{{ $mk->kode }}</td>
                <td>{{ $mk->nama_mata_kuliah }}</td>
                <td>{{ $mk->sks }}</td>
                <td>{{ $mk->semester }}</td>
                <td>
                    <a href="{{ route('mata-kuliah.edit', $mk->id) }}" class="btn">Ubah</a>

                    <form action="{{ route('mata-kuliah.destroy', $mk->id) }}" method="POST" style="display: inline"
                        onsubmit="return confirm('Yakin ingin menghapus data mata kuliah ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">Belum ada data mata kuliah.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>