<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalKuliahController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\UsersController;


Route::resource('mahasiswa', MahasiswaController::class);
Route::resource('dosen', DosenController::class);
Route::resource('mata-kuliah', MataKuliahController::class);
Route::resource('ruangan', RuanganController::class);
Route::resource('kelas', KelasController::class)->parameters([
    'kelas' => 'kls',
]);
Route::resource('jadwal-kuliah', JadwalKuliahController::class);


// Route login (tamu saja)
Route::middleware('guest')->group(function() {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class,'login']);
});
Route::post('/logout', [AuthController::class,'logout'])
    ->middleware('auth')->name('logout');

// Khusus admin (create, store, edit, update dan destroy)
Route::middleware(['auth', 'role:admin'])->group(function() {
    Route::resource('mahasiswa', MahasiswaController::class)->except(['index', 'show']);
    Route::resource('dosen', DosenController::class)->except(['index', 'show']);
    Route::resource('mata-kuliah', MataKuliahController::class)->except(['index', 'show']);
    Route::resource('ruangan', RuanganController::class)->except(['index', 'show']);
    Route::resource('kelas', KelasController::class)->except(['index', 'show']);
    Route::resource('jadwal-kuliah', JadwalKuliahController::class)->except(['index', 'show']);
});

// semua user login(read only index dan show)
Route::middleware('auth')->group(function() {
    Route::resource('mahasiswa', MahasiswaController::class)->only(['index', 'show']);
    Route::resource('dosen', DosenController::class)->only(['index', 'show']);
    Route::resource('mata-kuliah', MataKuliahController::class)->only(['index', 'show']);
    Route::resource('ruangan', RuanganController::class)->only(['index', 'show']);
    Route::resource('kelas', KelasController::class)->only(['index', 'show']);
    Route::resource('jadwal-kuliah', JadwalKuliahController::class)->only(['index', 'show']);
});

Route::middleware('role:admin')->group(function () {
        Route::resource('role', RoleController::class)->except('show');
        Route::resource('pengguna', UsersController::class)->except('show');
    });