<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Ruangan</title>
    <style>
    @include('partials.style')
    </style>
</head>

<body>
    @include('partials.nav')
    <h1>Data Ruangan</h1>

    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('ruangan.create') }}" class="btn">Tambah Ruangan</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Ruangan</th>
                <th>Nama</th>
                <th>Gedung</th>
                <th>Kapasitas</th>
                <th>Lab</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ruangan as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->kode_ruangan }}</td>
                <td>{{ $r->nama }}</td>
                <td>{{ $r->gedung }}</td>
                <td>{{ $r->kapasitas }}</td>
                <td>{{ $r->is_lab ? 'Ya' : 'Tidak' }}</td>
                <td>
                    <a href=" {{ route('ruangan.edit', $r->id) }}" class="btn">Ubah</a>

                    <form action="{{ route('ruangan.destroy', $r->id) }}" method="POST" style="display: inline"
                        onsubmit="return confirm('Yakin ingin menghapus data ruangan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">Belum ada data ruangan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>