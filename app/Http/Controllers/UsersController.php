<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->orderBy('id')->get();

        return view('pengguna.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('pengguna.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('pengguna.index')
            ->with('success', 'Data pengguna berhasil ditambahkan.');
    }

    public function edit(User $pengguna): View
    {
        $roles = Role::orderBy('name')->get();

        return view('pengguna.edit', compact('pengguna', 'roles'));
    }

    public function update(Request $request, User $pengguna): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($pengguna->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $pengguna->name = $validated['name'];
        $pengguna->email = $validated['email'];

        if (! empty($validated['password'])) {
            $pengguna->password = $validated['password'];
        }

        $pengguna->save();
        $pengguna->syncRoles([$validated['role']]);

        return redirect()
            ->route('pengguna.index')
            ->with('success', 'Data pengguna berhasil diubah.');
    }

    public function destroy(User $pengguna): RedirectResponse
    {
        if ($pengguna->is(auth()->user())) {
            return redirect()
                ->route('pengguna.index')
                ->with('error', 'Anda tidak bisa menghapus akun yang sedang digunakan.');
        }

        $pengguna->delete();

        return redirect()
            ->route('pengguna.index')
            ->with('success', 'Data pengguna berhasil dihapus.');
    }
}