<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>
    <style>
    @include('partials.style') body {
        max-width: 600px;
    }
    </style>
</head>

<body>
    <h1>Tambah Pengguna</h1>

    @if ($errors->any())
    <ul class="errors">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <form action="{{ route('pengguna.store') }}" method="POST">
        @csrf

        <p>
            <label for="name">Nama</label><br>
            <input type="text" id="name" name="name" value="{{ old('name') }}" maxlength="100" required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="100" required>
        </p>

        <p>
            <label for="password">Password</label><br>
            <input type="password" id="password" name="password" minlength="8" required>
        </p>

        <p>
            <label for="role">Role</label><br>
            <select id="role" name="role" required>
                <option value="">-- Pilih Role --</option>
                @foreach ($roles as $r)
                <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>
                    {{ $r->name }}
                </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('pengguna.index') }}" class="btn">Batal</a>
    </form>
</body>

</html>