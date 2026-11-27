<?php

// NIM: 252410102097 - Parisya Radita Dewantara
// Keamanan Otentikasi: Password Hashing (Bcrypt), Session Regeneration, & CSRF Protection

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectHome(Auth::user());
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:72'],
            'role' => ['required', 'in:klien,developer'],
        ]);

        User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return redirect()->route('login')->with('ok', 'Pendaftaran berhasil. Silakan masuk.');
    }

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectHome(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'max:1024'],
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));

        if (!Auth::attempt($credentials)) {
            return back()->withInput($request->only('email'))->with('err', 'Email atau password salah.');
        }

        $request->session()->regenerate();

        return $this->redirectHome(Auth::user())->with('ok', 'Selamat datang kembali.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('ok', 'Anda telah keluar.');
    }

    private function redirectHome(User $user): RedirectResponse
    {
        return redirect()->route($user->role === 'admin' ? 'admin.index' : 'dashboard');
    }
}