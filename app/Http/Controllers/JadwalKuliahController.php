<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use App\Models\LampiranJadwal;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalKuliahController extends Controller
{
    public function index(): View
    {
        $jadwalKuliah = JadwalKuliah::with(['kelas', 'mataKuliah', 'dosen', 'ruangan', 'lampiran'])->get();

        return view('jadwal_kuliah.index', compact('jadwalKuliah'));
    }

    public function create(): View
    {
        $kelas = Kelas::all();
        $mataKuliah = MataKuliah::all();
        $dosen = Dosen::all();
        $ruangan = Ruangan::all();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('jadwal_kuliah.create', compact('kelas', 'mataKuliah', 'dosen', 'ruangan', 'hari'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'dosen_id' => ['required', 'exists:dosen,id'],
            'ruangan_id' => ['required', 'exists:ruangan,id'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'lampiran.*' => ['nullable', 'file', 'max:2048'] 
        ]);

        $bentrokRuangan = JadwalKuliah::where('ruangan_id', $validated['ruangan_id'])
            ->where('hari', $validated['hari'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokRuangan) {
            return back()
                ->withInput()
                ->withErrors(['ruangan_id' => 'Ruangan sudah terpakai di jadwal tersebut.']);
        }

        $bentrokDosen = JadwalKuliah::where('dosen_id', $validated['dosen_id'])
            ->where('hari', $validated['hari'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokDosen) {
            return back()
                ->withInput()
                ->withErrors(['dosen_id' => 'Dosen sudah mengajar di jadwal tersebut.']);
        }

        $bentrokKelas = JadwalKuliah::where('kelas_id', $validated['kelas_id'])
            ->where('hari', $validated['hari'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokKelas) {
            return back()
                ->withInput()
                ->withErrors(['kelas_id' => 'Kelas sudah memiliki jadwal di waktu tersebut.']);
        }

        $jadwal = JadwalKuliah::create($validated);

        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $namaAsli = $file->getClientOriginalName();
                $path = $file->storeAs('lampiran_jadwal', Str::uuid() . '.' . $file->extension(), 'public');
                
                $jadwal->lampiran()->create([
                    'path' => $path,
                    'nama_asli' => $namaAsli
                ]);
            }
        }

        return redirect()
            ->route('jadwal-kuliah.index')
            ->with('success', 'Jadwal kuliah berhasil ditambahkan.');
    }

    public function edit(JadwalKuliah $jadwal_kuliah): View
    {
        $kelas = Kelas::all();
        $mataKuliah = MataKuliah::all();
        $dosen = Dosen::all();
        $ruangan = Ruangan::all();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('jadwal_kuliah.edit', compact('jadwal_kuliah', 'kelas', 'mataKuliah', 'dosen', 'ruangan', 'hari'));
    }

    public function update(Request $request, JadwalKuliah $jadwal_kuliah): RedirectResponse
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'dosen_id' => ['required', 'exists:dosen,id'],
            'ruangan_id' => ['required', 'exists:ruangan,id'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'lampiran.*' => ['nullable', 'file', 'max:2048'] 
        ]);

        $bentrokRuangan = JadwalKuliah::where('ruangan_id', $validated['ruangan_id'])
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $jadwal_kuliah->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokRuangan) {
            return back()
                ->withInput()
                ->withErrors(['ruangan_id' => 'Ruangan sudah terpakai di jadwal tersebut.']);
        }

        $bentrokDosen = JadwalKuliah::where('dosen_id', $validated['dosen_id'])
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $jadwal_kuliah->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokDosen) {
            return back()
                ->withInput()
                ->withErrors(['dosen_id' => 'Dosen sudah mengajar di jadwal tersebut.']);
        }

        $bentrokKelas = JadwalKuliah::where('kelas_id', $validated['kelas_id'])
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $jadwal_kuliah->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($bentrokKelas) {
            return back()
                ->withInput()
                ->withErrors(['kelas_id' => 'Kelas sudah memiliki jadwal di waktu tersebut.']);
        }

        $jadwal_kuliah->update($validated);

        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $namaAsli = $file->getClientOriginalName();
                $path = $file->storeAs('lampiran_jadwal', Str::uuid() . '.' . $file->extension(), 'public');
                
                $jadwal_kuliah->lampiran()->create([
                    'path' => $path,
                    'nama_asli' => $namaAsli
                ]);
            }
        }

        return redirect()
            ->route('jadwal-kuliah.index')
            ->with('success', 'Jadwal kuliah berhasil diubah.');
    }

    public function destroy(JadwalKuliah $jadwal_kuliah): RedirectResponse
    {
        foreach ($jadwal_kuliah->lampiran as $lampiran) {
            Storage::disk('public')->delete($lampiran->path);
        }
        
        $jadwal_kuliah->delete(); 

        return redirect()
            ->route('jadwal-kuliah.index')
            ->with('success', 'Jadwal kuliah beserta lampirannya berhasil dihapus.');
    }
}