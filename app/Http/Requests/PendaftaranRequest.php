<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim((string) $this->input('email'))),
            ]);
        }

        if ($this->has('email_confirmation')) {
            $this->merge([
                'email_confirmation' => strtolower(trim((string) $this->input('email_confirmation'))),
            ]);
        }

        if ($this->has('no_hp')) {
            $this->merge([
                'no_hp' => preg_replace('/[\s-]/', '', (string) $this->input('no_hp')),
            ]);
        }
    }

    public function rules(): array
    {
        return match ((int) $this->route('step')) {
            1 => $this->stepOneRules(),
            2 => $this->stepTwoRules(),
            3 => $this->stepThreeRules(),
            4 => $this->stepFourRules(),
            default => [],
        };
    }

    private function stepOneRules(): array
    {
        return [
            'email' => ['required', 'email', 'max:100', 'confirmed', Rule::unique('users', 'email')],
            'email_confirmation' => ['required', 'email'],
            'no_hp' => ['required', 'string', 'max:16', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    private function stepTwoRules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:100', "regex:/^[A-Za-z\\s.'-]+$/u"],
            'nik' => ['required', 'digits:16', Rule::unique('pendaftaran_mahasiswa', 'nik')],
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

    private function stepThreeRules(): array
    {
        return [
            'asal_sekolah' => ['required', 'string', 'max:100'],
            'jurusan_sekolah' => ['required', Rule::in(config('pendaftaran.jurusan_sekolah'))],
            'tahun_lulus' => ['required', 'integer', 'digits:4', 'min:1990', 'max:'.now()->year],
            'nilai_rata_rata' => ['required', 'numeric', 'between:0,100'],
            'program_studi' => ['required', Rule::in(config('pendaftaran.program_studi'))],
            'jenjang' => ['required', Rule::in(config('pendaftaran.jenjang'))],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'prestasi' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function stepFourRules(): array
    {
        return [
            'minat' => ['required', 'array', 'min:1', 'max:3'],
            'minat.*' => ['required', 'string', 'distinct', Rule::in(config('pendaftaran.minat'))],
            'sumber_informasi' => ['required', Rule::in(config('pendaftaran.sumber_informasi'))],
            'catatan' => ['nullable', 'string', 'max:500'],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=200,min_height=200'],
            'berkas' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'setuju_syarat' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.confirmed' => 'Konfirmasi email tidak sama.',
            'email.unique' => 'Email sudah terdaftar. Gunakan email lain.',
            'email_confirmation.required' => 'Konfirmasi email wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.max' => 'Nomor HP maksimal 16 karakter.',
            'no_hp.regex' => 'Nomor HP harus berformat Indonesia, contoh 081234567890.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.letters' => 'Password harus mengandung minimal satu huruf.',
            'password.numbers' => 'Password harus mengandung minimal satu angka.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 100 karakter.',
            'nama_lengkap.regex' => 'Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus terdiri dari tepat 10 digit angka.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Tanggal lahir tidak valid.',
            'tanggal_lahir.before_or_equal' => 'Usia minimal 15 tahun pada saat pendaftaran.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'agama.required' => 'Agama wajib dipilih.',
            'agama.in' => 'Pilihan agama tidak valid.',
            'kewarganegaraan.required' => 'Kewarganegaraan wajib dipilih.',
            'kewarganegaraan.in' => 'Pilihan kewarganegaraan tidak valid.',
            'negara_asal.required_if' => 'Negara asal wajib diisi untuk WNA.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.min' => 'Alamat minimal 10 karakter.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',
            'kode_pos.required' => 'Kode pos wajib diisi.',
            'kode_pos.digits' => 'Kode pos harus 5 digit angka.',
            'alamat_domisili.required_if' => 'Alamat domisili wajib diisi jika domisili berbeda.',
            'alamat_domisili.min' => 'Alamat domisili minimal 10 karakter.',
            'alamat_domisili.max' => 'Alamat domisili maksimal 255 karakter.',
            'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
            'jurusan_sekolah.required' => 'Jurusan sekolah wajib dipilih.',
            'jurusan_sekolah.in' => 'Pilihan jurusan sekolah tidak valid.',
            'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
            'tahun_lulus.integer' => 'Tahun lulus harus berupa angka.',
            'tahun_lulus.digits' => 'Tahun lulus harus 4 digit.',
            'tahun_lulus.min' => 'Tahun lulus minimal 1990.',
            'tahun_lulus.max' => 'Tahun lulus tidak boleh melebihi tahun berjalan.',
            'nilai_rata_rata.required' => 'Nilai rata-rata wajib diisi.',
            'nilai_rata_rata.numeric' => 'Nilai rata-rata harus berupa angka.',
            'nilai_rata_rata.between' => 'Nilai rata-rata harus antara 0 sampai 100.',
            'program_studi.required' => 'Program studi wajib dipilih.',
            'program_studi.in' => 'Pilihan program studi tidak valid.',
            'jenjang.required' => 'Jenjang wajib dipilih.',
            'jenjang.in' => 'Pilihan jenjang tidak valid.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak tersedia.',
            'prestasi.max' => 'Prestasi maksimal 500 karakter.',
            'minat.required' => 'Pilih minimal satu minat.',
            'minat.array' => 'Format minat tidak valid.',
            'minat.min' => 'Pilih minimal satu minat.',
            'minat.max' => 'Minat maksimal tiga pilihan.',
            'minat.*.in' => 'Pilihan minat tidak valid.',
            'minat.*.distinct' => 'Minat tidak boleh dipilih ganda.',
            'sumber_informasi.required' => 'Sumber informasi wajib dipilih.',
            'sumber_informasi.in' => 'Pilihan sumber informasi tidak valid.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
            'foto.required' => 'Foto wajib diunggah.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'foto.dimensions' => 'Foto minimal berukuran 200 x 200 piksel.',
            'berkas.required' => 'Berkas pendukung wajib diunggah.',
            'berkas.file' => 'Berkas tidak valid.',
            'berkas.mimes' => 'Berkas harus berformat PDF.',
            'berkas.max' => 'Ukuran berkas maksimal 5 MB.',
            'setuju_syarat.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'no_hp' => 'nomor HP',
            'nama_lengkap' => 'nama lengkap',
            'nik' => 'NIK',
            'nisn' => 'NISN',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'jenis_kelamin' => 'jenis kelamin',
            'kewarganegaraan' => 'kewarganegaraan',
            'negara_asal' => 'negara asal',
            'kode_pos' => 'kode pos',
            'alamat_domisili' => 'alamat domisili',
            'asal_sekolah' => 'asal sekolah',
            'jurusan_sekolah' => 'jurusan sekolah',
            'tahun_lulus' => 'tahun lulus',
            'nilai_rata_rata' => 'nilai rata-rata',
            'program_studi' => 'program studi',
            'kelas_id' => 'kelas',
            'sumber_informasi' => 'sumber informasi',
        ];
    }
}