<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    // Sesuaikan kolom lain sesuai tabel dosen Anda
    protected $fillable = ['nidn', 'nama', 'email', 'foto'];

    protected function fotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->foto ? asset('storage/'.$this->foto) : null
        );
    }
}