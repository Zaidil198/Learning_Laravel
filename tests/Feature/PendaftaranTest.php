<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\PendaftaranMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PendaftaranTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'mahasiswa', 'guard_name' => 'web']);
    }

    public function test_langkah_pertama_menolak_email_duplikat_nomor_hp_tidak_valid_dan_password_lemah(): void
    {
        User::factory()->create(['email' => 'admin@example.test']);

        $response = $this->from(route('registrasi.step', 1))
            ->post(route('registrasi.store', 1), [
                'email' => 'admin@example.test',
                'no_hp' => '12345',
                'password' => 'abcdefgh',
                'password_confirmation' => 'abcdefgh',
            ]);

        $response->assertRedirect(route('registrasi.step', 1));
        $response->assertSessionHasErrors(['email', 'no_hp', 'password']);
        $this->assertNull(session('registrasi.step_1'));
    }

    public function test_langkah_pertama_yang_valid_mengenkripsi_password_di_session(): void
    {
        $response = $this->post(route('registrasi.store', 1), $this->stepOneData());

        $response->assertRedirect(route('registrasi.step', 2));
        $response->assertSessionHas('registrasi.step_1');

        $stepOne = session('registrasi.step_1');
        $this->assertNotSame('rahasia123', $stepOne['password']);
    }

    public function test_tidak_bisa_melompati_langkah_sebelumnya(): void
    {
        $response = $this->get(route('registrasi.step', 3));

        $response->assertRedirect(route('registrasi.step', 1));
        $response->assertSessionHas('error');
    }

    public function test_langkah_kedua_menolak_nik_tidak_valid_dan_usia_di_bawah_15_tahun(): void
    {
        $this->post(route('registrasi.store', 1), $this->stepOneData());

        $response = $this->from(route('registrasi.step', 2))
            ->post(route('registrasi.store', 2), $this->stepTwoData([
                'nik' => '123',
                'tanggal_lahir' => now()->subYears(10)->format('Y-m-d'),
            ]));

        $response->assertRedirect(route('registrasi.step', 2));
        $response->assertSessionHasErrors(['nik', 'tanggal_lahir']);
    }

    public function test_wna_wajib_mengisi_negara_asal(): void
    {
        $this->post(route('registrasi.store', 1), $this->stepOneData());

        $response = $this->from(route('registrasi.step', 2))
            ->post(route('registrasi.store', 2), $this->stepTwoData([
                'kewarganegaraan' => 'WNA',
                'negara_asal' => '',
            ]));

        $response->assertRedirect(route('registrasi.step', 2));
        $response->assertSessionHasErrors('negara_asal');

        $response = $this->post(route('registrasi.store', 2), $this->stepTwoData([
            'kewarganegaraan' => 'WNA',
            'negara_asal' => 'Malaysia',
        ]));

        $response->assertRedirect(route('registrasi.step', 3));
    }

    public function test_langkah_keempat_menolak_file_yang_tidak_lengkap(): void
    {
        Storage::fake('public');
        $this->completeFirstThreeSteps();

        $response = $this->from(route('registrasi.step', 4))
            ->post(route('registrasi.store', 4), [
                'minat' => ['Teknologi'],
                'sumber_informasi' => 'Website',
            ]);

        $response->assertRedirect(route('registrasi.step', 4));
        $response->assertSessionHasErrors(['foto', 'berkas', 'setuju_syarat']);
    }

    public function test_pendaftaran_lengkap_menyimpan_user_pendaftaran_file_dan_role(): void
    {
        Storage::fake('public');
        $this->completeFirstThreeSteps();

        $response = $this->post(route('registrasi.store', 4), $this->stepFourData());

        $response->assertRedirect(route('registrasi.sukses'));

        $user = User::where('email', 'calon@example.test')->firstOrFail();
        $this->assertTrue($user->hasRole('mahasiswa'));

        $pendaftaran = PendaftaranMahasiswa::firstOrFail();
        $this->assertSame($user->id, $pendaftaran->user_id);
        $this->assertSame('3573010101010001', $pendaftaran->nik);
        $this->assertSame(['Teknologi', 'Olahraga'], $pendaftaran->minat);
        $this->assertTrue($pendaftaran->setuju_syarat);

        Storage::disk('public')->assertExists($pendaftaran->foto);
        Storage::disk('public')->assertExists($pendaftaran->berkas);
        $this->assertNull(session('registrasi'));

        $this->get(route('registrasi.sukses'))
            ->assertOk()
            ->assertSee($pendaftaran->nomor_pendaftaran);
    }

    private function completeFirstThreeSteps(): void
    {
        $this->post(route('registrasi.store', 1), $this->stepOneData());
        $this->post(route('registrasi.store', 2), $this->stepTwoData());

        $kelas = Kelas::create([
            'nama_kelas' => 'ASE11',
            'angkatan' => now()->year,
            'jurusan' => 'Teknik Informatika',
        ]);

        $this->post(route('registrasi.store', 3), $this->stepThreeData($kelas));
    }

    private function stepOneData(array $override = []): array
    {
        return array_merge([
            'email' => 'calon@example.test',
            'no_hp' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ], $override);
    }

    private function stepTwoData(array $override = []): array
    {
        return array_merge([
            'nama_lengkap' => 'Budi Santoso',
            'nik' => '3573010101010001',
            'tempat_lahir' => 'Malang',
            'tanggal_lahir' => now()->subYears(20)->format('Y-m-d'),
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'kewarganegaraan' => 'WNI',
            'negara_asal' => '',
            'alamat' => 'Jl. Merdeka No. 10, Malang',
            'kode_pos' => '65111',
        ], $override);
    }

    private function stepThreeData(Kelas $kelas, array $override = []): array
    {
        return array_merge([
            'asal_sekolah' => 'SMA Negeri 1 Malang',
            'jurusan_sekolah' => 'IPA',
            'tahun_lulus' => now()->subYears(2)->year,
            'nilai_rata_rata' => '88.50',
            'program_studi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'kelas_id' => $kelas->id,
            'prestasi' => 'Juara 1 Olimpiade Matematika',
        ], $override);
    }

    private function stepFourData(array $override = []): array
    {
        return array_merge([
            'minat' => ['Teknologi', 'Olahraga'],
            'sumber_informasi' => 'Website',
            'catatan' => 'Membutuhkan informasi beasiswa.',
            'foto' => UploadedFile::fake()->image('foto.jpg', 600, 600),
            'berkas' => UploadedFile::fake()->create('ijazah.pdf', 200, 'application/pdf'),
            'setuju_syarat' => '1',
        ], $override);
    }
}