<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RuanganController extends Controller
{
    public function index(): View
    {
        $ruangan = Ruangan::all();

        return view('ruangan.index', compact('ruangan'));
    }

    public function create(): View
    {
        return view('ruangan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_ruangan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('ruangan', 'kode_ruangan'),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'gedung' => ['required', 'string', 'max:100'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:9999'],
            'is_lab' => ['nullable', 'boolean'],
        ]);

        $validated['is_lab'] = $request->has('is_lab');

        Ruangan::create($validated);

        return redirect()
            ->route('ruangan.index')
            ->with('success', 'Data ruangan berhasil ditambahkan.');
    }

    public function edit(Ruangan $ruangan): View
    {
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, Ruangan $ruangan): RedirectResponse
    {
        $validated = $request->validate([
            'kode_ruangan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('ruangan', 'kode_ruangan')->ignore($ruangan->id),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'gedung' => ['required', 'string', 'max:100'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:9999'],
            'is_lab' => ['nullable', 'boolean'],
        ]);

        $validated['is_lab'] = $request->has('is_lab');

        $ruangan->update($validated);

        return redirect()
            ->route('ruangan.index')
            ->with('success', 'Data ruangan berhasil diubah.');
    }

    public function destroy(Ruangan $ruangan): RedirectResponse
    {
        $ruangan->delete();

        return redirect()
            ->route('ruangan.index')
            ->with('success', 'Data ruangan berhasil dihapus.');
    }
}
