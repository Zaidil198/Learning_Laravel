<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna</title>
    <style>
    @include('partials.style')
    </style>
</head>

<body>
    @include('partials.nav')

    <h1>Data Pengguna</h1>

    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    @if (session('error'))
    <ul class="errors">
        <li>{{ session('error') }}</li>
    </ul>
    @endif


    <p>
        <a href="{{ route('pengguna.create') }}" class="btn">Tambah Pengguna</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->getRoleNames()->implode(', ') ?: '-' }}</td>
                <td>
                    @role('admin')
                    <a href="{{ route('pengguna.edit', $user->id) }}" class="btn">Ubah</a>

                    <form action="{{ route('pengguna.destroy', $user->id) }}" method="POST" style="display: inline"
                        onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
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
                <td colspan="5">Belum ada data pengguna.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>