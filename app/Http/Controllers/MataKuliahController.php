<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MataKuliahController extends Controller
{
    public function index(): View
    {
        $mataKuliah = MataKuliah::all();

        return view('mata_kuliah.index', compact('mataKuliah'));
    }

    public function create(): View
    {
        return view('mata_kuliah.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mata_kuliah', 'kode'),
            ],
            'nama_mata_kuliah' => ['required', 'string', 'max:100'],
            'sks' => ['required', 'integer', 'min:1', 'max:10'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'modul' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('modul')) {
        $file = $request->file('modul');
        $path = $file->storeAs('modul', Str::uuid() . '.pdf', 'public');
        $validated['modul'] = $path;
        }

        MataKuliah::create($validated);

        return redirect()
            ->route('mata-kuliah.index')
            ->with('success', 'Data mata kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $mata_kuliah): View
    {
        return view('mata_kuliah.edit', compact('mata_kuliah'));
    }

    public function update(Request $request, MataKuliah $mata_kuliah): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mata_kuliah', 'kode')->ignore($mata_kuliah->id),
            ],
            'nama_mata_kuliah' => ['required', 'string', 'max:100'],
            'sks' => ['required', 'integer', 'min:1', 'max:10'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'modul' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $mata_kuliah->update($validated);

        return redirect()
            ->route('mata-kuliah.index')
            ->with('success', 'Data mata kuliah berhasil diubah.');
    }

    public function destroy(MataKuliah $mata_kuliah): RedirectResponse
    {
        if ($mata_kuliah->modul) {
        Storage::disk('public')->delete($mata_kuliah->modul);
        }
        $mata_kuliah->delete();

        return redirect()
            ->route('mata-kuliah.index')
            ->with('success', 'Data mata kuliah berhasil dihapus.');
    }
}