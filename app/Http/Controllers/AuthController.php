<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use HasRoles;
    
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->intended(route('mahasiswa.index'));
        } 
        
        if ($user->hasRole('mahasiswa')) {
            return redirect()->intended(route('jadwal-kuliah.index'));
        }

        return redirect()->intended('/');
    }

        throw ValidationException::withMessages([
            'email' => ['Email atau password salah.'],
        ]);
        }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}