# Dokumentasi Implementasi CRUD Simulasi Penjadwalan Kuliah

**Tanggal Implementasi:** 26 September 2026  
**Project:** Laravel - Simulasi Penjadwalan Kuliah

---

## Daftar Isi
1. [Pendahuluan](#pendahuluan)
2. [CRUD Dosen](#crud-dosen)
3. [CRUD Mahasiswa](#crud-mahasiswa)
4. [CRUD Mata Kuliah](#crud-mata-kuliah)
5. [CRUD Ruangan](#crud-ruangan)
6. [CRUD Kelas](#crud-kelas)
7. [CRUD Jadwal Kuliah](#crud-jadwal-kuliah)
8. [Perintah Laravel yang Digunakan](#perintah-laravel)
9. [Struktur Database](#struktur-database)

---

## Pendahuluan

Implementasi ini bertujuan untuk membuat sistem simulasi penjadwalan kuliah dengan entitas:
- **Dosen** - Data pengajar
- **Mahasiswa** - Data pelajar (dengan relasi ke Kelas)
- **Mata Kuliah** - Data mata kuliah yang ditawarkan
- **Ruangan** - Data ruang kelas (dengan flag is_lab)
- **Kelas** - Data kelas (nama, angkatan, jurusan)
- **Jadwal Kuliah** - Penjadwalan dengan validasi bentrok

**Relasi Utama:**
- Mahasiswa belongsTo Kelas
- JadwalKuliah belongsTo Kelas, MataKuliah, Dosen, Ruangan
- Validasi bentrok: Ruangan, Dosen, dan Kelas tidak boleh double booking

---

## CRUD Dosen

### Migration

**File:** `database/migrations/2026_09_12_000000_create_dosen_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table): void {
            $table->id();
            $table->string('nidn', 20)->unique();
            $table->string('nama', 100);
            $table->string('bidang_keahlian', 100);
            $table->string('email', 100)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
```

### Model

**File:** `app/Models/Dosen.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = [
        'nidn',
        'nama',
        'bidang_keahlian',
        'email',
        'no_telepon',
    ];
}
```

### Controller

**File:** `app/Http/Controllers/DosenController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DosenController extends Controller
{
    public function index(): View
    {
        $dosen = Dosen::all();

        return view('dosen.index', compact('dosen'));
    }

    public function create(): View
    {
        return view('dosen.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nidn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('dosen', 'nidn'),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'bidang_keahlian' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        Dosen::create($validated);

        return redirect()
            ->route('dosen.index')
            ->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(Dosen $dosen): View
    {
        return view('dosen.edit', compact('dosen'));
    }

    public function update(Request $request, Dosen $dosen): RedirectResponse
    {
        $validated = $request->validate([
            'nidn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('dosen', 'nidn')->ignore($dosen->id),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'bidang_keahlian' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $dosen->update($validated);

        return redirect()
            ->route('dosen.index')
            ->with('success', 'Data dosen berhasil diubah.');
    }

    public function destroy(Dosen $dosen): RedirectResponse
    {
        $dosen->delete();

        return redirect()
            ->route('dosen.index')
            ->with('success', 'Data dosen berhasil dihapus.');
    }
}
```

### Views

**File:** `resources/views/dosen/index.blade.php` **[UPDATED]**

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen</title>
    <style>
        @include('partials.style')
    </style>
</head>
<body>
    <h1>Data Dosen</h1>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('dosen.create') }}">Tambah Dosen</a>
        |
        <a href="{{ route('mahasiswa.index') }}">Data Mahasiswa</a>
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
                <th>NIDN</th>
                <th>Nama</th>
                <th>Bidang Keahlian</th>
                <th>Email</th>
                <th>No. Telepon</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dosen as $dsn)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $dsn->nidn }}</td>
                    <td>{{ $dsn->nama }}</td>
                    <td>{{ $dsn->bidang_keahlian }}</td>
                    <td>{{ $dsn->email }}</td>
                    <td>{{ $dsn->no_telepon }}</td>
                    <td>
                        <a href="{{ route('dosen.edit', $dsn->id) }}" class="btn">Ubah</a>

                        <form
                            action="{{ route('dosen.destroy', $dsn->id) }}"
                            method="POST"
                            style="display: inline"
                            onsubmit="return confirm('Yakin ingin menghapus data dosen ini?')"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada data dosen.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
```

**File:** `resources/views/dosen/create.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dosen</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Tambah Dosen</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('dosen.store') }}" method="POST">
        @csrf

        <p>
            <label for="nidn">NIDN</label><br>
            <input type="text" id="nidn" name="nidn" value="{{ old('nidn') }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" maxlength="100" required>
        </p>

        <p>
            <label for="bidang_keahlian">Bidang Keahlian</label><br>
            <input type="text" id="bidang_keahlian" name="bidang_keahlian" value="{{ old('bidang_keahlian') }}" maxlength="100" required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="100">
        </p>

        <p>
            <label for="no_telepon">No. Telepon</label><br>
            <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" maxlength="20">
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('dosen.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

**File:** `resources/views/dosen/edit.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Dosen</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Ubah Data Dosen</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('dosen.update', $dosen->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="nidn">NIDN</label><br>
            <input type="text" id="nidn" name="nidn" value="{{ old('nidn', $dosen->nidn) }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $dosen->nama) }}" maxlength="100" required>
        </p>

        <p>
            <label for="bidang_keahlian">Bidang Keahlian</label><br>
            <input type="text" id="bidang_keahlian" name="bidang_keahlian" value="{{ old('bidang_keahlian', $dosen->bidang_keahlian) }}" maxlength="100" required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email', $dosen->email) }}" maxlength="100">
        </p>

        <p>
            <label for="no_telepon">No. Telepon</label><br>
            <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $dosen->no_telepon) }}" maxlength="20">
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('dosen.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

---

## CRUD Mahasiswa

### Migration

**File:** `database/migrations/2026_09_05_081458_create_mahasiswa_table.php` **[UPDATED]**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table): void {
            $table->id();
            $table->string('nim', 20)->unique();
            $table->string('nama', 100);
            $table->string('jurusan', 100);
            $table->string('email', 100)->nullable();
            $table->unsignedSmallInteger('angkatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
```

**File:** `database/migrations/2026_09_26_000005_add_kelas_id_to_mahasiswa_table.php` (Penambahan)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table): void {
            $table->foreignId('kelas_id')->nullable()->after('angkatan')->constrained('kelas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table): void {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
};
```

### Model

**File:** `app/Models/Mahasiswa.php` **[UPDATED]**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'nim',
        'nama',
        'jurusan',
        'email',
        'angkatan',
        'kelas_id',
    ];

    protected function casts(): array
    {
        return [
            'angkatan' => 'integer',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
```

### Controller

**File:** `app/Http/Controllers/MahasiswaController.php` **[UPDATED]**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function index(): View
    {
        $mahasiswa = Mahasiswa::with('kelas')->get();

        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function create(): View
    {
        $kelas = Kelas::all();

        return view('mahasiswa.create', compact('kelas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nim' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mahasiswa', 'nim'),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', 'string', 'max:100'],
            'angkatan' => ['required', 'integer', 'min:1900', 'max:2200'],
            'email' => ['nullable', 'email', 'max:100'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ]);

        Mahasiswa::create($validated);

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        $kelas = Kelas::all();

        return view('mahasiswa.edit', compact('mahasiswa', 'kelas'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $validated = $request->validate([
            'nim' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mahasiswa', 'nim')->ignore($mahasiswa->id),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', 'string', 'max:100'],
            'angkatan' => ['required', 'integer', 'min:1900', 'max:2200'],
            'email' => ['nullable', 'email', 'max:100'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ]);

        $mahasiswa->update($validated);

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil diubah.');
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->delete();

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil dihapus.');
    }
}
```

### Views

**File:** `resources/views/mahasiswa/index.blade.php` **[UPDATED]**

```blade
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
    <h1>Data Mahasiswa</h1>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('mahasiswa.create') }}">Tambah Mahasiswa</a>
        |
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
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $mhs->nim }}</td>
                    <td>{{ $mhs->nama }}</td>
                    <td>{{ $mhs->jurusan }}</td>
                    <td>{{ $mhs->angkatan }}</td>
                    <td>{{ $mhs->email }}</td>
                    <td>{{ $mhs->kelas->nama_kelas ?? '-' }}</td>
                    <td>
                        <a href="{{ route('mahasiswa.edit', $mhs->id) }}" class="btn">Ubah</a>

                        <form
                            action="{{ route('mahasiswa.destroy', $mhs->id) }}"
                            method="POST"
                            style="display: inline"
                            onsubmit="return confirm('Yakin ingin menghapus data mahasiswa ini?')"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Belum ada data mahasiswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
```

**File:** `resources/views/mahasiswa/create.blade.php` **[UPDATED]**

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Tambah Mahasiswa</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('mahasiswa.store') }}" method="POST">
        @csrf

        <p>
            <label for="nim">NIM</label><br>
            <input type="text" id="nim" name="nim" value="{{ old('nim') }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" maxlength="100" required>
        </p>

        <p>
            <label for="jurusan">Jurusan</label><br>
            <input type="text" id="jurusan" name="jurusan" value="{{ old('jurusan') }}" maxlength="100" required>
        </p>

        <p>
            <label for="angkatan">Angkatan</label><br>
            <input type="number" id="angkatan" name="angkatan" value="{{ old('angkatan') }}" min="1900" max="2200" required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="100">
        </p>

        <p>
            <label for="kelas_id">Kelas</label><br>
            <select id="kelas_id" name="kelas_id">
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('mahasiswa.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

**File:** `resources/views/mahasiswa/edit.blade.php` **[UPDATED]**

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Mahasiswa</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Ubah Data Mahasiswa</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('mahasiswa.update', $mahasiswa->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="nim">NIM</label><br>
            <input type="text" id="nim" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" maxlength="100" required>
        </p>

        <p>
            <label for="jurusan">Jurusan</label><br>
            <input type="text" id="jurusan" name="jurusan" value="{{ old('jurusan', $mahasiswa->jurusan) }}" maxlength="100" required>
        </p>

        <p>
            <label for="angkatan">Angkatan</label><br>
            <input type="number" id="angkatan" name="angkatan" value="{{ old('angkatan', $mahasiswa->angkatan) }}" min="1900" max="2200" required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email', $mahasiswa->email) }}" maxlength="100">
        </p>

        <p>
            <label for="kelas_id">Kelas</label><br>
            <select id="kelas_id" name="kelas_id">
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ old('kelas_id', $mahasiswa->kelas_id) == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('mahasiswa.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

---

## CRUD Mata Kuliah

### Migration

**File:** `database/migrations/2026_09_26_000001_create_mata_kuliah_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table): void {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama_mata_kuliah', 100);
            $table->unsignedTinyInteger('sks');
            $table->unsignedTinyInteger('semester');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
```

### Model

**File:** `app/Models/MataKuliah.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah';

    protected $fillable = [
        'kode',
        'nama_mata_kuliah',
        'sks',
        'semester',
    ];

    protected function casts(): array
    {
        return [
            'sks' => 'integer',
            'semester' => 'integer',
        ];
    }

    public function jadwalKuliah(): HasMany
    {
        return $this->hasMany(JadwalKuliah::class);
    }
}
```

### Controller

**File:** `app/Http/Controllers/MataKuliahController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MataKuliahController extends Controller
{
    public function index(): View
    {
        $mataKuliah = MataKuliah::all();

        return view('mata_kuliah.index', compact('mataKuliah'));
    }

    public function create(): View
    {
        return view('mata_kuliah.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mata_kuliah', 'kode'),
            ],
            'nama_mata_kuliah' => ['required', 'string', 'max:100'],
            'sks' => ['required', 'integer', 'min:1', 'max:10'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
        ]);

        MataKuliah::create($validated);

        return redirect()
            ->route('mata-kuliah.index')
            ->with('success', 'Data mata kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $mata_kuliah): View
    {
        return view('mata_kuliah.edit', compact('mata_kuliah'));
    }

    public function update(Request $request, MataKuliah $mata_kuliah): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mata_kuliah', 'kode')->ignore($mata_kuliah->id),
            ],
            'nama_mata_kuliah' => ['required', 'string', 'max:100'],
            'sks' => ['required', 'integer', 'min:1', 'max:10'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
        ]);

        $mata_kuliah->update($validated);

        return redirect()
            ->route('mata-kuliah.index')
            ->with('success', 'Data mata kuliah berhasil diubah.');
    }

    public function destroy(MataKuliah $mata_kuliah): RedirectResponse
    {
        $mata_kuliah->delete();

        return redirect()
            ->route('mata-kuliah.index')
            ->with('success', 'Data mata kuliah berhasil dihapus.');
    }
}
```

### Views

**File:** `resources/views/mata_kuliah/index.blade.php`

```blade
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
    <h1>Data Mata Kuliah</h1>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('mata-kuliah.create') }}">Tambah Mata Kuliah</a>
        |
        <a href="{{ route('dosen.index') }}">Data Dosen</a>
        |
        <a href="{{ route('mahasiswa.index') }}">Data Mahasiswa</a>
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
                    <td>{{ $mk->kode }}</td>
                    <td>{{ $mk->nama_mata_kuliah }}</td>
                    <td>{{ $mk->sks }}</td>
                    <td>{{ $mk->semester }}</td>
                    <td>
                        <a href="{{ route('mata-kuliah.edit', $mk->id) }}" class="btn">Ubah</a>

                        <form
                            action="{{ route('mata-kuliah.destroy', $mk->id) }}"
                            method="POST"
                            style="display: inline"
                            onsubmit="return confirm('Yakin ingin menghapus data mata kuliah ini?')"
                        >
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
```

**File:** `resources/views/mata_kuliah/create.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Kuliah</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Tambah Mata Kuliah</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('mata-kuliah.store') }}" method="POST">
        @csrf

        <p>
            <label for="kode">Kode</label><br>
            <input type="text" id="kode" name="kode" value="{{ old('kode') }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama_mata_kuliah">Nama Mata Kuliah</label><br>
            <input type="text" id="nama_mata_kuliah" name="nama_mata_kuliah" value="{{ old('nama_mata_kuliah') }}" maxlength="100" required>
        </p>

        <p>
            <label for="sks">SKS</label><br>
            <input type="number" id="sks" name="sks" value="{{ old('sks') }}" min="1" max="10" required>
        </p>

        <p>
            <label for="semester">Semester</label><br>
            <input type="number" id="semester" name="semester" value="{{ old('semester') }}" min="1" max="14" required>
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('mata-kuliah.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

**File:** `resources/views/mata_kuliah/edit.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Mata Kuliah</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Ubah Data Mata Kuliah</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('mata-kuliah.update', $mata_kuliah->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="kode">Kode</label><br>
            <input type="text" id="kode" name="kode" value="{{ old('kode', $mata_kuliah->kode) }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama_mata_kuliah">Nama Mata Kuliah</label><br>
            <input type="text" id="nama_mata_kuliah" name="nama_mata_kuliah" value="{{ old('nama_mata_kuliah', $mata_kuliah->nama_mata_kuliah) }}" maxlength="100" required>
        </p>

        <p>
            <label for="sks">SKS</label><br>
            <input type="number" id="sks" name="sks" value="{{ old('sks', $mata_kuliah->sks) }}" min="1" max="10" required>
        </p>

        <p>
            <label for="semester">Semester</label><br>
            <input type="number" id="semester" name="semester" value="{{ old('semester', $mata_kuliah->semester) }}" min="1" max="14" required>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('mata-kuliah.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

---

## CRUD Ruangan

### Migration

**File:** `database/migrations/2026_09_26_000002_create_ruangan_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangan', function (Blueprint $table): void {
            $table->id();
            $table->string('kode_ruangan', 20)->unique();
            $table->string('nama', 100);
            $table->string('gedung', 100);
            $table->unsignedSmallInteger('kapasitas');
            $table->boolean('is_lab')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
```

### Model

**File:** `app/Models/Ruangan.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';

    protected $fillable = [
        'kode_ruangan',
        'nama',
        'gedung',
        'kapasitas',
        'is_lab',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
            'is_lab' => 'boolean',
        ];
    }

    public function jadwalKuliah(): HasMany
    {
        return $this->hasMany(JadwalKuliah::class);
    }
}
```

### Controller

**File:** `app/Http/Controllers/RuanganController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RuanganController extends Controller
{
    public function index(): View
    {
        $ruangan = Ruangan::all();

        return view('ruangan.index', compact('ruangan'));
    }

    public function create(): View
    {
        return view('ruangan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_ruangan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('ruangan', 'kode_ruangan'),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'gedung' => ['required', 'string', 'max:100'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:9999'],
            'is_lab' => ['nullable', 'boolean'],
        ]);

        $validated['is_lab'] = $request->has('is_lab');

        Ruangan::create($validated);

        return redirect()
            ->route('ruangan.index')
            ->with('success', 'Data ruangan berhasil ditambahkan.');
    }

    public function edit(Ruangan $ruangan): View
    {
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, Ruangan $ruangan): RedirectResponse
    {
        $validated = $request->validate([
            'kode_ruangan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('ruangan', 'kode_ruangan')->ignore($ruangan->id),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'gedung' => ['required', 'string', 'max:100'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:9999'],
            'is_lab' => ['nullable', 'boolean'],
        ]);

        $validated['is_lab'] = $request->has('is_lab');

        $ruangan->update($validated);

        return redirect()
            ->route('ruangan.index')
            ->with('success', 'Data ruangan berhasil diubah.');
    }

    public function destroy(Ruangan $ruangan): RedirectResponse
    {
        $ruangan->delete();

        return redirect()
            ->route('ruangan.index')
            ->with('success', 'Data ruangan berhasil dihapus.');
    }
}
```

### Views

**File:** `resources/views/ruangan/index.blade.php`

```blade
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
    <h1>Data Ruangan</h1>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('ruangan.create') }}">Tambah Ruangan</a>
        |
        <a href="{{ route('dosen.index') }}">Data Dosen</a>
        |
        <a href="{{ route('mahasiswa.index') }}">Data Mahasiswa</a>
        |
        <a href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a>
        |
        <a href="{{ route('kelas.index') }}">Kelas</a>
        |
        <a href="{{ route('jadwal-kuliah.index') }}">Jadwal Kuliah</a>
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
                        <a href="{{ route('ruangan.edit', $r->id) }}" class="btn">Ubah</a>

                        <form
                            action="{{ route('ruangan.destroy', $r->id) }}"
                            method="POST"
                            style="display: inline"
                            onsubmit="return confirm('Yakin ingin menghapus data ruangan ini?')"
                        >
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
```

**File:** `resources/views/ruangan/create.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ruangan</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Tambah Ruangan</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('ruangan.store') }}" method="POST">
        @csrf

        <p>
            <label for="kode_ruangan">Kode Ruangan</label><br>
            <input type="text" id="kode_ruangan" name="kode_ruangan" value="{{ old('kode_ruangan') }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" maxlength="100" required>
        </p>

        <p>
            <label for="gedung">Gedung</label><br>
            <input type="text" id="gedung" name="gedung" value="{{ old('gedung') }}" maxlength="100" required>
        </p>

        <p>
            <label for="kapasitas">Kapasitas</label><br>
            <input type="number" id="kapasitas" name="kapasitas" value="{{ old('kapasitas') }}" min="1" max="9999" required>
        </p>

        <p>
            <label style="font-weight: normal;">
                <input type="checkbox" name="is_lab" value="1" {{ old('is_lab') ? 'checked' : '' }}>
                Ruangan Lab
            </label>
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('ruangan.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

**File:** `resources/views/ruangan/edit.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Ruangan</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Ubah Data Ruangan</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="kode_ruangan">Kode Ruangan</label><br>
            <input type="text" id="kode_ruangan" name="kode_ruangan" value="{{ old('kode_ruangan', $ruangan->kode_ruangan) }}" maxlength="20" required>
        </p>

        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $ruangan->nama) }}" maxlength="100" required>
        </p>

        <p>
            <label for="gedung">Gedung</label><br>
            <input type="text" id="gedung" name="gedung" value="{{ old('gedung', $ruangan->gedung) }}" maxlength="100" required>
        </p>

        <p>
            <label for="kapasitas">Kapasitas</label><br>
            <input type="number" id="kapasitas" name="kapasitas" value="{{ old('kapasitas', $ruangan->kapasitas) }}" min="1" max="9999" required>
        </p>

        <p>
            <label style="font-weight: normal;">
                <input type="checkbox" name="is_lab" value="1" {{ old('is_lab', $ruangan->is_lab) ? 'checked' : '' }}>
                Ruangan Lab
            </label>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('ruangan.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

---

## CRUD Kelas

### Migration

**File:** `database/migrations/2026_09_26_000003_create_kelas_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_kelas', 50)->unique();
            $table->unsignedSmallInteger('angkatan');
            $table->string('jurusan', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
```

### Model

**File:** `app/Models/Kelas.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'angkatan',
        'jurusan',
    ];

    protected function casts(): array
    {
        return [
            'angkatan' => 'integer',
        ];
    }

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function jadwalKuliah(): HasMany
    {
        return $this->hasMany(JadwalKuliah::class);
    }
}
```

### Controller

**File:** `app/Http/Controllers/KelasController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(): View
    {
        $kelas = Kelas::all();

        return view('kelas.index', compact('kelas'));
    }

    public function create(): View
    {
        return view('kelas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas'),
            ],
            'angkatan' => ['required', 'integer', 'min:10', 'max:20'],
            'jurusan' => ['required', 'string', 'max:100'],
        ]);

        Kelas::create($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kls): View
    {
        return view('kelas.edit', compact('kls'));
    }

    public function update(Request $request, Kelas $kls): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas')->ignore($kls->id),
            ],
            'angkatan' => ['required', 'integer', 'min:10', 'max:20'],
            'jurusan' => ['required', 'string', 'max:100'],
        ]);

        $kls->update($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Data kelas berhasil diubah.');
    }

    public function destroy(Kelas $kls): RedirectResponse
    {
        $kls->delete();

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
```

### Views

**File:** `resources/views/kelas/index.blade.php`

```blade
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

                        <form
                            action="{{ route('kelas.destroy', $k->id) }}"
                            method="POST"
                            style="display: inline"
                            onsubmit="return confirm('Yakin ingin menghapus data kelas ini?')"
                        >
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
```

**File:** `resources/views/kelas/create.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Tambah Kelas</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('kelas.store') }}" method="POST">
        @csrf

        <p>
            <label for="nama_kelas">Nama Kelas</label><br>
            <input type="text" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas') }}" maxlength="50" required>
        </p>

        <p>
            <label for="angkatan">Angkatan</label><br>
            <input type="number" id="angkatan" name="angkatan" value="{{ old('angkatan') }}" min="10" max="20" required>
        </p>

        <p>
            <label for="jurusan">Jurusan</label><br>
            <input type="text" id="jurusan" name="jurusan" value="{{ old('jurusan') }}" maxlength="100" required>
        </p>

        <button type="submit">Simpan</button>
        <a href="{{ route('kelas.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

**File:** `resources/views/kelas/edit.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Kelas</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Ubah Data Kelas</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('kelas.update', $kls->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="nama_kelas">Nama Kelas</label><br>
            <input type="text" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas', $kls->nama_kelas) }}" maxlength="50" required>
        </p>

        <p>
            <label for="angkatan">Angkatan</label><br>
            <input type="number" id="angkatan" name="angkatan" value="{{ old('angkatan', $kls->angkatan) }}" min="10" max="20" required>
        </p>

        <p>
            <label for="jurusan">Jurusan</label><br>
            <input type="text" id="jurusan" name="jurusan" value="{{ old('jurusan', $kls->jurusan) }}" maxlength="100" required>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('kelas.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

---

## CRUD Jadwal Kuliah

### Migration

**File:** `database/migrations/2026_09_26_000004_create_jadwal_kuliah_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_kuliah', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('dosen')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangan')->cascadeOnDelete();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kuliah');
    }
};
```

### Model

**File:** `app/Models/JadwalKuliah.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalKuliah extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kuliah';

    protected $fillable = [
        'kelas_id',
        'mata_kuliah_id',
        'dosen_id',
        'ruangan_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }
}
```

### Controller

**File:** `app/Http/Controllers/JadwalKuliahController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalKuliahController extends Controller
{
    public function index(): View
    {
        $jadwalKuliah = JadwalKuliah::with(['kelas', 'mataKuliah', 'dosen', 'ruangan'])->get();

        return view('jadwal_kuliah.index', compact('jadwalKuliah'));
    }

    public function create(): View
    {
        $kelas = Kelas::all();
        $mataKuliah = MataKuliah::all();
        $dosen = Dosen::all();
        $ruangan = Ruangan::all();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('jadwal_kuliah.create', compact('kelas', 'mataKuliah', 'dosen', 'ruangan', 'hari'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'dosen_id' => ['required', 'exists:dosen,id'],
            'ruangan_id' => ['required', 'exists:ruangan,id'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        // Validasi bentrok ruangan
        $bentrokRuangan = JadwalKuliah::where('ruangan_id', $validated['ruangan_id'])
            ->where('hari', $validated['hari'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokRuangan) {
            return back()
                ->withInput()
                ->withErrors(['ruangan_id' => 'Ruangan sudah terpakai di jadwal tersebut.']);
        }

        // Validasi bentrok dosen
        $bentrokDosen = JadwalKuliah::where('dosen_id', $validated['dosen_id'])
            ->where('hari', $validated['hari'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokDosen) {
            return back()
                ->withInput()
                ->withErrors(['dosen_id' => 'Dosen sudah mengajar di jadwal tersebut.']);
        }

        // Validasi bentrok kelas
        $bentrokKelas = JadwalKuliah::where('kelas_id', $validated['kelas_id'])
            ->where('hari', $validated['hari'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokKelas) {
            return back()
                ->withInput()
                ->withErrors(['kelas_id' => 'Kelas sudah memiliki jadwal di waktu tersebut.']);
        }

        JadwalKuliah::create($validated);

        return redirect()
            ->route('jadwal-kuliah.index')
            ->with('success', 'Jadwal kuliah berhasil ditambahkan.');
    }

    public function edit(JadwalKuliah $jadwal_kuliah): View
    {
        $kelas = Kelas::all();
        $mataKuliah = MataKuliah::all();
        $dosen = Dosen::all();
        $ruangan = Ruangan::all();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('jadwal_kuliah.edit', compact('jadwal_kuliah', 'kelas', 'mataKuliah', 'dosen', 'ruangan', 'hari'));
    }

    public function update(Request $request, JadwalKuliah $jadwal_kuliah): RedirectResponse
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'dosen_id' => ['required', 'exists:dosen,id'],
            'ruangan_id' => ['required', 'exists:ruangan,id'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        // Validasi bentrok ruangan (exclude current)
        $bentrokRuangan = JadwalKuliah::where('ruangan_id', $validated['ruangan_id'])
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $jadwal_kuliah->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokRuangan) {
            return back()
                ->withInput()
                ->withErrors(['ruangan_id' => 'Ruangan sudah terpakai di jadwal tersebut.']);
        }

        // Validasi bentrok dosen (exclude current)
        $bentrokDosen = JadwalKuliah::where('dosen_id', $validated['dosen_id'])
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $jadwal_kuliah->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokDosen) {
            return back()
                ->withInput()
                ->withErrors(['dosen_id' => 'Dosen sudah mengajar di jadwal tersebut.']);
        }

        // Validasi bentrok kelas (exclude current)
        $bentrokKelas = JadwalKuliah::where('kelas_id', $validated['kelas_id'])
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $jadwal_kuliah->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokKelas) {
            return back()
                ->withInput()
                ->withErrors(['kelas_id' => 'Kelas sudah memiliki jadwal di waktu tersebut.']);
        }

        $jadwal_kuliah->update($validated);

        return redirect()
            ->route('jadwal-kuliah.index')
            ->with('success', 'Jadwal kuliah berhasil diubah.');
    }

    public function destroy(JadwalKuliah $jadwal_kuliah): RedirectResponse
    {
        $jadwal_kuliah->delete();

        return redirect()
            ->route('jadwal-kuliah.index')
            ->with('success', 'Jadwal kuliah berhasil dihapus.');
    }
}
```

### Views

**File:** `resources/views/jadwal_kuliah/index.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kuliah</title>
    <style>
        @include('partials.style')
        body {
            max-width: 1200px;
        }
    </style>
</head>
<body>
    <h1>Jadwal Kuliah</h1>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('jadwal-kuliah.create') }}">Tambah Jadwal</a>
        |
        <a href="{{ route('dosen.index') }}">Data Dosen</a>
        |
        <a href="{{ route('mahasiswa.index') }}">Data Mahasiswa</a>
        |
        <a href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a>
        |
        <a href="{{ route('ruangan.index') }}">Ruangan</a>
        |
        <a href="{{ route('kelas.index') }}">Kelas</a>
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
                    <td>{{ $jk->kelas->nama_kelas }}</td>
                    <td>{{ $jk->mataKuliah->nama_mata_kuliah }}</td>
                    <td>{{ $jk->dosen->nama }}</td>
                    <td>{{ $jk->ruangan->nama }} ({{ $jk->ruangan->kode_ruangan }})</td>
                    <td>{{ $jk->hari }}</td>
                    <td>{{ substr($jk->jam_mulai, 0, 5) }} - {{ substr($jk->jam_selesai, 0, 5) }}</td>
                    <td>
                        <a href="{{ route('jadwal-kuliah.edit', $jk->id) }}" class="btn">Ubah</a>

                        <form
                            action="{{ route('jadwal-kuliah.destroy', $jk->id) }}"
                            method="POST"
                            style="display: inline"
                            onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')"
                        >
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
```

**File:** `resources/views/jadwal_kuliah/create.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal Kuliah</title>
    <style>
        @include('partials.style')
        body {
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

        <button type="submit">Simpan</button>
        <a href="{{ route('jadwal-kuliah.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

**File:** `resources/views/jadwal_kuliah/edit.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Jadwal Kuliah</title>
    <style>
        @include('partials.style')
        body {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Ubah Jadwal Kuliah</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('jadwal-kuliah.update', $jadwal_kuliah->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="kelas_id">Kelas</label><br>
            <select id="kelas_id" name="kelas_id" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ old('kelas_id', $jadwal_kuliah->kelas_id) == $k->id ? 'selected' : '' }}>
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
                    <option value="{{ $mk->id }}" {{ old('mata_kuliah_id', $jadwal_kuliah->mata_kuliah_id) == $mk->id ? 'selected' : '' }}>
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
                    <option value="{{ $dsn->id }}" {{ old('dosen_id', $jadwal_kuliah->dosen_id) == $dsn->id ? 'selected' : '' }}>
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
                    <option value="{{ $r->id }}" {{ old('ruangan_id', $jadwal_kuliah->ruangan_id) == $r->id ? 'selected' : '' }}>
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
                    <option value="{{ $h }}" {{ old('hari', $jadwal_kuliah->hari) == $h ? 'selected' : '' }}>
                        {{ $h }}
                    </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="jam_mulai">Jam Mulai</label><br>
            <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', substr($jadwal_kuliah->jam_mulai, 0, 5)) }}" required>
        </p>

        <p>
            <label for="jam_selesai">Jam Selesai</label><br>
            <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', substr($jadwal_kuliah->jam_selesai, 0, 5)) }}" required>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('jadwal-kuliah.index') }}" class="btn">Batal</a>
    </form>
</body>
</html>
```

---

## File Pendukung

### CSS Partial

**File:** `resources/views/partials/style.blade.php`

```blade
body {
    font-family: Arial, sans-serif;
    max-width: 1100px;
    margin: 30px auto;
    padding: 0 20px;
}

h1 {
    margin-bottom: 10px;
}

a {
    color: #155eef;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th,
td {
    border: 1px solid #999;
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

.success {
    padding: 10px;
    background-color: #e8f7e8;
    color: #176b17;
}

form {
    display: inline;
}

label {
    display: inline-block;
    margin-bottom: 5px;
    font-weight: bold;
}

input,
select {
    width: 100%;
    box-sizing: border-box;
    padding: 9px;
}

input[type="checkbox"] {
    width: auto;
    padding: 0;
    margin-right: 6px;
}

button,
.btn {
    padding: 8px 14px;
    background-color: #f0f0f0;
    border: 1px solid #999;
    cursor: pointer;
    font-family: Arial, sans-serif;
    font-size: 14px;
    color: #000;
    text-decoration: none;
    display: inline-block;
}

button:hover,
.btn:hover {
    background-color: #e0e0e0;
}

.errors {
    padding: 10px 10px 10px 30px;
    background-color: #ffecec;
    color: #a40000;
}
```

### Routes

**File:** `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalKuliahController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\RuanganController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('mahasiswa', MahasiswaController::class);
Route::resource('dosen', DosenController::class);
Route::resource('mata-kuliah', MataKuliahController::class);
Route::resource('ruangan', RuanganController::class);
Route::resource('kelas', KelasController::class)->parameters([
    'kelas' => 'kls',
]);
Route::resource('jadwal-kuliah', JadwalKuliahController::class);
```

---

## Perintah Laravel

### 1. Membuat Migration

```bash
php artisan make:migration create_mata_kuliah_table
php artisan make:migration create_ruangan_table
php artisan make:migration create_kelas_table
php artisan make:migration create_jadwal_kuliah_table
php artisan make:migration add_kelas_id_to_mahasiswa_table
```

### 2. Membuat Model

```bash
php artisan make:model MataKuliah
php artisan make:model Ruangan
php artisan make:model Kelas
php artisan make:model JadwalKuliah
```

### 3. Membuat Controller

```bash
php artisan make:controller MataKuliahController --resource
php artisan make:controller RuanganController --resource
php artisan make:controller KelasController --resource
php artisan make:controller JadwalKuliahController --resource
```

### 4. Menjalankan Migration

```bash
php artisan migrate
```

### 5. Melihat Route List

```bash
php artisan route:list
```

### 6. Menjalankan Server

```bash
php artisan serve
```

---

## Struktur Database

### Tabel Dosen
| Field | Type | Constraint |
|-------|------|------------|
| id | bigint | primary key |
| nidn | varchar(20) | unique |
| nama | varchar(100) | not null |
| bidang_keahlian | varchar(100) | not null |
| email | varchar(100) | nullable |
| no_telepon | varchar(20) | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel Mahasiswa
| Field | Type | Constraint |
|-------|------|------------|
| id | bigint | primary key |
| nim | varchar(20) | unique |
| nama | varchar(100) | not null |
| jurusan | varchar(100) | not null |
| email | varchar(100) | nullable |
| angkatan | smallint | not null |
| kelas_id | bigint | foreign key (nullable) |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel Mata Kuliah
| Field | Type | Constraint |
|-------|------|------------|
| id | bigint | primary key |
| kode | varchar(20) | unique |
| nama_mata_kuliah | varchar(100) | not null |
| sks | tinyint | not null |
| semester | tinyint | not null |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel Ruangan
| Field | Type | Constraint |
|-------|------|------------|
| id | bigint | primary key |
| kode_ruangan | varchar(20) | unique |
| nama | varchar(100) | not null |
| gedung | varchar(100) | not null |
| kapasitas | smallint | not null |
| is_lab | boolean | default false |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel Kelas
| Field | Type | Constraint |
|-------|------|------------|
| id | bigint | primary key |
| nama_kelas | varchar(50) | unique |
| angkatan | smallint | not null |
| jurusan | varchar(100) | not null |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel Jadwal Kuliah
| Field | Type | Constraint |
|-------|------|------------|
| id | bigint | primary key |
| kelas_id | bigint | foreign key |
| mata_kuliah_id | bigint | foreign key |
| dosen_id | bigint | foreign key |
| ruangan_id | bigint | foreign key |
| hari | enum | not null |
| jam_mulai | time | not null |
| jam_selesai | time | not null |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## Relasi Database

```
Mahasiswa ──belongsTo──> Kelas
Kelas ──hasMany──> Mahasiswa
Kelas ──hasMany──> JadwalKuliah

JadwalKuliah ──belongsTo──> Kelas
JadwalKuliah ──belongsTo──> MataKuliah
JadwalKuliah ──belongsTo──> Dosen
JadwalKuliah ──belongsTo──> Ruangan

MataKuliah ──hasMany──> JadwalKuliah
Dosen ──hasMany──> JadwalKuliah
Ruangan ──hasMany──> JadwalKuliah
```

---

## Validasi Bentrok Jadwal

Sistem melakukan validasi untuk mencegah:
1. **Ruangan bentrok** - 1 ruangan tidak bisa dipakai 2 kelas di hari & jam yang sama
2. **Dosen bentrok** - 1 dosen tidak bisa mengajar 2 kelas di hari & jam yang sama
3. **Kelas bentrok** - 1 kelas tidak bisa punya 2 jadwal di hari & jam yang sama

Validasi menggunakan query dengan `whereBetween` untuk mengecek overlap waktu.

---

## Catatan Implementasi

1. **CSS Partial**: Semua view menggunakan `@include('partials.style')` untuk konsistensi styling
2. **Route Parameter**: Kelas menggunakan parameter `kls` (bukan `kela`) untuk singkatan yang lebih baik
3. **Checkbox Lab**: Menggunakan `font-weight: normal` pada label agar tidak bold
4. **Button Styling**: Semua button (Ubah, Hapus, Simpan, Batal) menggunakan style yang sama
5. **Eager Loading**: JadwalKuliah menggunakan `with()` untuk menghindari N+1 query problem

---

**Dokumentasi selesai dibuat pada:** 26 September 2026
