<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(): View
    {
        $kelas = Kelas::all();

        return view('kelas.index', compact('kelas'));
    }

    public function create(): View
    {
        return view('kelas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas'),
            ],
            'angkatan' => ['required', 'integer', 'min:1990', 'max:2200'],
            'jurusan' => ['required', 'string', 'max:100'],
        ]);

        Kelas::create($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kls): View
    {
        return view('kelas.edit', compact('kls'));
    }

    public function update(Request $request, Kelas $kls): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kelas', 'nama_kelas')->ignore($kls->id),
            ],
            'angkatan' => ['required', 'integer', 'min:1990', 'max:2200'],
            'jurusan' => ['required', 'string', 'max:100'],
        ]);

        $kls->update($validated);

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Data kelas berhasil diubah.');
    }

    public function destroy(Kelas $kls): RedirectResponse
    {
        $kls->delete();

        return redirect()
            ->route('kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}