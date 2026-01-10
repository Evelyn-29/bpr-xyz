<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi login + reCAPTCHA
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        // Attempt login
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Regenerate session
        $request->session()->regenerate();

        // Ambil user dan arahkan berdasarkan role
        $user = Auth::user();

        // 1. Cek Superadmin / Staff Internal DULUAN
        // Kita pakai hasAnyRole (jika pakai Spatie) atau cek satu-satu
        if ($user->hasRole(['Superadmin', 'Admin', 'Manager', 'Direktur'])) {
            return redirect()->route('app.dashboard');
        }

        // 2. Baru Cek Nasabah
        elseif ($user->hasRole('Nasabah')) {
            return redirect()->route('nasabah.dashboard');
        }

        // Fallback (jika role tidak dikenali)
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'email' => 'Role pengguna tidak dikenali. Hubungi administrator.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // Kembali ke halaman login
    }
}
