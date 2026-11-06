<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Role</title>
    <style>
    @include('partials.style') body {
        max-width: 600px;
    }
    </style>
</head>

<body>
    <h1>Ubah Data Role</h1>

    @if ($errors->any())
    <ul class="errors">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <form action="{{ route('role.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="name">Nama Role</label><br>
            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" maxlength="50" required>
        </p>

        <p>
            <label>Permission</label>
        </p>

        @foreach ($permissions as $permission)
        <p>
            <label style="font-weight: normal;">
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                    {{ in_array($permission->name, old('permissions', $role->permissions->pluck('name')->toArray())) ? 'checked' : '' }}>
                {{ $permission->name }}
            </label>
        </p>
        @endforeach

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('role.index') }}" class="btn">Batal</a>
    </form>
</body>

</html>