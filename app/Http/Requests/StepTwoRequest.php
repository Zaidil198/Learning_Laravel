<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidNikProvinsi;

class StepTwoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:100', "regex:/^[A-Za-z\\s.'-]+$/u"],
            'nik' => ['required', 'digits:16', new ValidNikProvinsi(), Rule::unique('pendaftaran_mahasiswa', 'nik')],
            'nisn' => ['required', 'digits:10', Rule::unique('pendaftaran_mahasiswa', 'nisn')],
            'tempat_lahir' => ['required', 'string', 'max:60'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:'.now()->subYears(15)->format('Y-m-d')],
            'jenis_kelamin' => ['required', Rule::in(array_keys(config('pendaftaran.jenis_kelamin')))],
            'agama' => ['required', Rule::in(config('pendaftaran.agama'))],
            'kewarganegaraan' => ['required', Rule::in(array_keys(config('pendaftaran.kewarganegaraan')))],
            'negara_asal' => ['nullable', 'required_if:kewarganegaraan,WNA', 'string', 'max:60'],
            'alamat' => ['required', 'string', 'min:10', 'max:255'],
            'kode_pos' => ['required', 'digits:5'],
            'domisili_berbeda' => ['nullable', 'boolean'],
            'alamat_domisili' => ['nullable', 'required_if:domisili_berbeda,1', 'string', 'min:10', 'max:255'],
        ];
    }
}