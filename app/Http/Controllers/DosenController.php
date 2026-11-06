<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    public function __construct(private ImageUploadService $imageUpload) {}

    public function index()
    {
        $dosen = Dosen::orderBy('id')->paginate(10);
        return view('dosen.index', compact('dosen'));
    }

    public function create()
    {
        return view('dosen.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateDosen($request);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->imageUpload->store($request->file('foto'));
        }

        Dosen::create($validated);
        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(Dosen $dosen)
    {
        return view('dosen.edit', compact('dosen'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $validated = $this->validateDosen($request, $dosen);
        $fotoLama = $dosen->foto;

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->imageUpload->store($request->file('foto'));
        }
        $dosen->update($validated);

        if ($request->hasFile('foto')) {
            $this->imageUpload->delete($fotoLama);
        }

        return redirect()
        ->route('dosen.index')
        ->with('success', 'Data dosen berhasil diubah.');
    }

    public function destroy(Dosen $dosen)
    {
        $this->imageUpload->delete($dosen->foto);
        $dosen->delete();
        return redirect()
        ->route('dosen.index'
        )->with('success', 'Data dosen berhasil dihapus.');
    }

    private function validateDosen(Request $request, ?Dosen $dosen = null): array
    {
        return $request->validate([
            'nidn' => ['required', 'string', 'max:20', Rule::unique('dosen', 'nidn')->ignore($dosen)],
            'nama' => ['required', 'string', 'max:100'],
            'bidang_keahlian' =>['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nidn.required' => 'NIDN wajib diisi.',
            'nidn.unique' => 'NIDN sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'bidang_keahlian' => 'Bidang Keahlian wajib diisi.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);
    }
}