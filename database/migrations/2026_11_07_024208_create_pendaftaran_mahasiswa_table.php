<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftaran_mahasiswa', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nomor_pendaftaran', 30)->unique();
            $table->string('nama_lengkap', 100);
            $table->char('nik', 16)->unique();
            $table->string('tempat_lahir', 60);
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin', 1);
            $table->string('agama', 20);
            $table->string('kewarganegaraan', 3);
            $table->string('negara_asal', 60)->nullable();
            $table->string('no_hp', 16);
            $table->text('alamat');
            $table->char('kode_pos', 5);
            $table->string('asal_sekolah', 100);
            $table->string('jurusan_sekolah', 50);
            $table->unsignedSmallInteger('tahun_lulus');
            $table->decimal('nilai_rata_rata', 5, 2);
            $table->string('program_studi', 100);
            $table->string('jenjang', 5);
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->json('minat');
            $table->string('sumber_informasi', 50);
            $table->text('catatan')->nullable();
            $table->text('prestasi')->nullable();
            $table->string('foto')->nullable();
            $table->string('berkas')->nullable();
            $table->boolean('setuju_syarat')->default(false);
            $table->string('status', 20)->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_mahasiswa');
    }
};