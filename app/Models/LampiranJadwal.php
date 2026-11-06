<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LampiranJadwal extends Model
{
    protected $table = 'lampiran_jadwal';
    protected $fillable = ['jadwal_kuliah_id', 'path', 'nama_asli'];

    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class);
    }
}