<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class ValidNikProvinsi implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $kodeProvinsi = substr($value, 0, 2);
        
        $validProvinsi = ['31', '32', '33', '34', '35', '36', '51', '52', '53']; 

        if (!in_array($kodeProvinsi, $validProvinsi, true)) {
            $fail('Kode wilayah/provinsi pada 2 digit awal NIK tidak valid.');
        }
    }
}