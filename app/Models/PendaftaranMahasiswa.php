<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendaftaranMahasiswa extends Model
{
    protected $table = 'pendaftaran_mahasiswa';

    protected $fillable = [
        'user_id',
        'nomor_pendaftaran',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'kewarganegaraan',
        'negara_asal',
        'no_hp',
        'alamat',
        'kode_pos',
        'asal_sekolah',
        'jurusan_sekolah',
        'tahun_lulus',
        'nilai_rata_rata',
        'program_studi',
        'jenjang',
        'kelas_id',
        'minat',
        'sumber_informasi',
        'catatan',
        'prestasi',
        'foto',
        'berkas',
        'setuju_syarat',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'nilai_rata_rata' => 'decimal:2',
            'minat' => 'array',
            'setuju_syarat' => 'boolean',
        ];
    }

    protected function fotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->foto ? asset('storage/'.$this->foto) : null
        );
    }

    protected function berkasUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->berkas ? asset('storage/'.$this->berkas) : null
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}