<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran_mahasiswa', function (Blueprint $table): void {
            $table->char('nisn', 10)->unique()->after('nik');

            $table->boolean('domisili_berbeda')->default(false)->after('kode_pos');$table->text('alamat_domisili')->nullable()->after('domisili_berbeda');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_mahasiswa', function (Blueprint $table): void {$table->dropColumn([
                'nisn',
                'domisili_berbeda',
                'alamat_domisili',
            ]);
        });
    }
};