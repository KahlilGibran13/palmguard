<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Halaman Landing Page (Public - Tanpa Login)
     */
    public function landingPage()
    {
        // Jika sudah login, langsung arahkan ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('pages.Landing');   // ← Pastikan nama file sesuai (case-sensitive)
    }

    /**
     * Halaman Login
     */
    public function loginPage()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.login');
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan role (opsional, bisa disesuaikan)
            // if ($user->isManager()) {
            //     return redirect()->route('dashboard')->with('success', 'Selamat datang, Manager!');
            // }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');   // ← Ubah ke landing page setelah logout
    }
}