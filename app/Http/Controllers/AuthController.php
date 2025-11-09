<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

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

        // Arahkan sesuai role
        switch ($user->role) {
            case 'tu':
                return redirect()->route('tu.dashboard');
            case 'dosen':
                return redirect()->route('dosen.dashboard');
            case 'koordinator':
                return redirect()->route('kaprodi.dashboard');
            default:
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Role tidak dikenali.']);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}


