<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranRequest;
use App\Models\Kelas;
use App\Models\PendaftaranMahasiswa;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    private const STEPS = [1, 2, 3, 4];

    public function __construct(private ImageUploadService $imageUpload) {}

    public function start(Request $request): RedirectResponse
    {
        for ($step = 1; $step <= 3; $step++) {
            if (! $request->session()->has("registrasi.step_$step")) {
                return redirect()->route('registrasi.step', $step);
            }
        }

        return redirect()->route('registrasi.step', 4);
    }

    public function show(Request $request, int $step): View|RedirectResponse
    {
        $this->ensureStepExists($step);

        if ($redirect = $this->ensurePreviousStepsCompleted($step)) {
            return $redirect;
        }

        return view('registrasi.index', [
            'step' => $step,
            'data' => $request->session()->get('registrasi.data', []),
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
            'completedSteps' => $this->completedSteps($request),
        ]);
    }

    public function store(PendaftaranRequest $request, int $step): RedirectResponse
    {
        $this->ensureStepExists($step);

        if ($redirect = $this->ensurePreviousStepsCompleted($step)) {
            return $redirect;
        }

        $validated = $request->validated();

        if ($step === 1) {
            $validated['password'] = Crypt::encryptString($validated['password']);
        }

        if ($step < 4) {
            $request->session()->put("registrasi.step_$step", $validated);
            $request->session()->put('registrasi.data', array_merge(
                $request->session()->get('registrasi.data', []),
                $validated
            ));

            return redirect()
                ->route('registrasi.step', $step + 1)
                ->with('success', "Langkah $step berhasil disimpan. Silakan lanjutkan.");
        }

        return $this->finalize($request, $validated);
    }

    public function success(Request $request): View|RedirectResponse
    {
        $hasil = $request->session()->get('registrasi_sukses');

        if (! is_array($hasil)) {
            return redirect()->route('registrasi.start');
        }

        return view('registrasi.success', ['hasil' => $hasil]);
    }

    private function finalize(PendaftaranRequest $request, array $stepFour): RedirectResponse
    {
        $data = array_merge(
            $request->session()->get('registrasi.data', []),
            $stepFour
        );

        $request->merge([
            'email' => $data['email'],
            'nik' => $data['nik'],
        ]);

        $request->validate([
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')],
            'nik' => ['required', 'digits:16', Rule::unique('pendaftaran_mahasiswa', 'nik')],
        ], [
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
            'nik.unique' => 'NIK sudah terdaftar. Silakan periksa kembali data Anda.',
        ]);

        $fotoPath = null;
        $berkasPath = null;

        try {
            $fotoPath = $this->imageUpload->store($request->file('foto'), 'pendaftaran');
            $berkasPath = $request->file('berkas')->store('pendaftaran/berkas', 'public');

            $pendaftaran = DB::transaction(function () use ($data, $fotoPath, $berkasPath): PendaftaranMahasiswa {
                $user = User::create([
                    'name' => $data['nama_lengkap'],
                    'email' => $data['email'],
                    'password' => Crypt::decryptString($data['password']),
                ]);

                $user->assignRole('mahasiswa');

                return PendaftaranMahasiswa::create([
                    'user_id' => $user->id,
                    'nomor_pendaftaran' => $this->generateNomorPendaftaran(),
                    'nama_lengkap' => $data['nama_lengkap'],
                    'nik' => $data['nik'],
                    'tempat_lahir' => $data['tempat_lahir'],
                    'tanggal_lahir' => $data['tanggal_lahir'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'agama' => $data['agama'],
                    'kewarganegaraan' => $data['kewarganegaraan'],
                    'negara_asal' => $data['negara_asal'] ?? null,
                    'no_hp' => $data['no_hp'],
                    'alamat' => $data['alamat'],
                    'kode_pos' => $data['kode_pos'],
                    'asal_sekolah' => $data['asal_sekolah'],
                    'jurusan_sekolah' => $data['jurusan_sekolah'],
                    'tahun_lulus' => $data['tahun_lulus'],
                    'nilai_rata_rata' => $data['nilai_rata_rata'],
                    'program_studi' => $data['program_studi'],
                    'jenjang' => $data['jenjang'],
                    'kelas_id' => $data['kelas_id'],
                    'minat' => $data['minat'],
                    'sumber_informasi' => $data['sumber_informasi'],
                    'catatan' => $data['catatan'] ?? null,
                    'prestasi' => $data['prestasi'] ?? null,
                    'foto' => $fotoPath,
                    'berkas' => $berkasPath,
                    'setuju_syarat' => true,
                ]);
            });
        } catch (\Throwable $e) {
            $this->imageUpload->delete($fotoPath);

            if ($berkasPath) {
                Storage::disk('public')->delete($berkasPath);
            }

            throw $e;
        }

        $request->session()->forget('registrasi');
        $request->session()->flash('registrasi_sukses', [
            'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
            'nama_lengkap' => $pendaftaran->nama_lengkap,
            'email' => $data['email'],
            'program_studi' => $pendaftaran->program_studi,
            'jenjang' => $pendaftaran->jenjang,
        ]);

        return redirect()->route('registrasi.sukses');
    }

    private function ensureStepExists(int $step): void
    {
        abort_unless(in_array($step, self::STEPS, true), 404);
    }

    private function ensurePreviousStepsCompleted(int $step): ?RedirectResponse
    {
        for ($i = 1; $i < $step; $i++) {
            if (! session()->has("registrasi.step_$i")) {
                return redirect()
                    ->route('registrasi.step', $i)
                    ->with('error', "Langkah $i belum selesai. Silakan lengkapi terlebih dahulu.");
            }
        }

        return null;
    }

    private function completedSteps(Request $request): array
    {
        return array_values(array_filter(
            self::STEPS,
            fn (int $step): bool => $request->session()->has("registrasi.step_$step")
        ));
    }

    private function generateNomorPendaftaran(): string
    {
        do {
            $nomor = 'PMB-'.now()->format('Y').'-'.Str::upper(Str::random(6));
        } while (PendaftaranMahasiswa::where('nomor_pendaftaran', $nomor)->exists());

        return $nomor;
    }
}