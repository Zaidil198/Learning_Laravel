<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Role</title>
    <style>
    @include('partials.style')
    </style>
</head>

<body>
    @include('partials.nav')
    <h1>Data Role</h1>

    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    @if (session('error'))
    <ul class="errors">
        <li>{{ session('error') }}</li>
    </ul>
    @endif

    @role('admin')
    <p>
        <a href="{{ route('role.create') }}" class="btn">Tambah Role</a>
    </p>
    @endrole

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Role</th>
                <th>Permission</th>
                <th>Jumlah Pengguna</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $role)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->permissions->pluck('name')->implode(', ') ?: '-' }}</td>
                <td>{{ $role->users_count }}</td>
                <td>
                    @role('admin')
                    <a href="{{ route('role.edit', $role->id) }}" class="btn">Ubah</a>

                    <form action="{{ route('role.destroy', $role->id) }}" method="POST" style="display: inline"
                        onsubmit="return confirm('Yakin ingin menghapus role ini?')">
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
                <td colspan="5">Belum ada data role.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>