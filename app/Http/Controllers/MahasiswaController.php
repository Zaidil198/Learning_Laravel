<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function __construct(private ImageUploadService $imageUpload)
    {
    }

    public function index(): View
    {
        $mahasiswa = Mahasiswa::with('kelas')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function create(): View
    {
        $kelas = Kelas::all();

        return view('mahasiswa.create', compact('kelas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMahasiswa($request);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->imageUpload->store($request->file('foto'));
        }

        Mahasiswa::create($validated);

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        $kelas = Kelas::all();

        return view('mahasiswa.edit', compact('mahasiswa', 'kelas'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $validated = $this->validateMahasiswa($request, $mahasiswa);
        $fotoLama = $mahasiswa->foto;

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->imageUpload->store($request->file('foto'));
        }

        $mahasiswa->update($validated);

        if ($request->hasFile('foto')) {
            $this->imageUpload->delete($fotoLama);
        }

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil diubah.');
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $this->imageUpload->delete($mahasiswa->foto);
        $mahasiswa->delete();

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    private function validateMahasiswa(Request $request, ?Mahasiswa $mahasiswa = null): array
    {
        return $request->validate([
            'nim' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mahasiswa', 'nim')->ignore($mahasiswa),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', 'string', 'max:100'],
            'angkatan' => ['required', 'integer', 'min:1900', 'max:2200'],
            'email' => ['nullable', 'email', 'max:100'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'jurusan.required' => 'Jurusan wajib diisi.',
            'angkatan.required' => 'Angkatan wajib diisi.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);
    }
}