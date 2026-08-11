<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Login manual dengan email dan password
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }

        Auth::login($user);
        return $this->redirectByRole($user);
    }

    /**
     * Redirect ke halaman Google Login
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();

            if (!str_ends_with($googleUser->getEmail(), '@polban.ac.id')) {
                return redirect('/login')->with('error', 'Hanya email @polban.ac.id yang diizinkan.');
            }

            $user = \App\Models\User::where('email', $googleUser->getEmail())->first();
            if (!$user) {
                return redirect('/login')->with('error', 'Akun belum terdaftar di sistem.');
            }

            // ... Konfigurasi guard sudah benar, tapi Session::start() tidak diperlukan
            // karena sudah dihandle oleh middleware 'web'

            Auth::guard('web')->login($user, true);
            
            // ✅ BARIS DD() SUDAH DIHAPUS

            // Redirect sesuai role
            return $this->redirectByRole($user); // ✅ Redirect berjalan normal

        } catch (\Exception $e) {
            // Anda bisa tambahkan Log::error($e->getMessage()); di sini
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login dengan Google.');
        }
    }


    /**
     * Logout user
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Helper redirect sesuai role
     */
    private function redirectByRole($user)
    {
        switch ($user->role) {
            case 'tu':
                return redirect()->route('tu.dashboard');
            case 'koordinator':
                return redirect()->route('kaprodi.dashboard');
            case 'dosen':
                return redirect()->route('dosen.dashboard');
            default:
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Role tidak dikenali.']);
        }
    }
}
