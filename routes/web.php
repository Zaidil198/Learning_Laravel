<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalKuliahController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    /*
    |----------------------------------------------------------------------
    | Registrasi mahasiswa baru: 4 langkah, data sementara di session.
    |----------------------------------------------------------------------
    */
    Route::get('/registrasi', [PendaftaranController::class, 'start'])->name('registrasi.start');
    Route::get('/registrasi/step/{step}', [PendaftaranController::class, 'show'])
        ->whereNumber('step')
        ->name('registrasi.step');
    Route::post('/registrasi/step/{step}', [PendaftaranController::class, 'store'])
        ->whereNumber('step')
        ->name('registrasi.store');
    Route::get('/registrasi/sukses', [PendaftaranController::class, 'success'])->name('registrasi.sukses');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Route Menu Konten: dijaga permission "menu-<slug>"
|--------------------------------------------------------------------------
| Menu yang boleh tampil diatur per role lewat permission menu (RBAC Control).
| Aksi tulis (create/store/edit/update/destroy) tetap khusus role admin.
| Grup tulis didaftarkan LEBIH DULU agar "create" tidak tertangkap "{id}".
*/
Route::middleware('auth')->group(function () {
    Route::middleware('permission:menu-mahasiswa')->group(function () {
        Route::resource('mahasiswa', MahasiswaController::class)->except(['index', 'show'])->middleware('role:admin');
        Route::resource('mahasiswa', MahasiswaController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:menu-dosen')->group(function () {
        Route::resource('dosen', DosenController::class)->except(['index', 'show'])->middleware('role:admin');
        Route::resource('dosen', DosenController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:menu-mata-kuliah')->group(function () {
        Route::resource('mata-kuliah', MataKuliahController::class)->except(['index', 'show'])->middleware('role:admin');
        Route::resource('mata-kuliah', MataKuliahController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:menu-ruangan')->group(function () {
        Route::resource('ruangan', RuanganController::class)->except(['index', 'show'])->middleware('role:admin');
        Route::resource('ruangan', RuanganController::class)->only(['index', 'show']);
    });

    Route::middleware('permission:menu-kelas')->group(function () {
        Route::resource('kelas', KelasController::class)->except(['index', 'show'])->middleware('role:admin')->parameters([
            'kelas' => 'kls',
        ]);
        Route::resource('kelas', KelasController::class)->only(['index', 'show'])->parameters([
            'kelas' => 'kls',
        ]);
    });

    Route::middleware('permission:menu-jadwal-kuliah')->group(function () {
        Route::resource('jadwal-kuliah', JadwalKuliahController::class)->except(['index', 'show'])->middleware('role:admin');
        Route::resource('jadwal-kuliah', JadwalKuliahController::class)->only(['index', 'show']);
    });

    /*
    |----------------------------------------------------------------------
    | Manajemen RBAC (role) & pengguna: selalu khusus admin (pengaman)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('role', RoleController::class)->except('show');
        Route::resource('pengguna', UsersController::class)->except('show');
    });
});