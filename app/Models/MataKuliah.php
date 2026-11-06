<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;
    protected $table = 'mata_kuliah';
    protected $fillable = ['kode_mk', 'nama_mk', 'sks', 'modul'];

    protected function modulUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->modul ? asset('storage/'.$this->modul) : null
        );
    }
}