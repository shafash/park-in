<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\TbLogAktivitas;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $maxAttempts = 5; // maximum attempts
        $decaySeconds = 60; // lockout time in seconds

        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ])->withInput();
        }

        $user = \App\Models\User::where('username', $credentials['username'])
            ->where('status_aktif', 1)
            ->first();

        $plain = $credentials['password'];

        $invalidResponse = function() use ($throttleKey, $decaySeconds) {
            RateLimiter::hit($throttleKey, $decaySeconds);
            return back()->withErrors([
                'username' => 'Username atau password salah, atau akun nonaktif.'
            ])->withInput();
        };

        if (!$user) {
            return $invalidResponse();
        }

        if (!Hash::check($plain, $user->password)) {
            // Legacy MD5 support: if stored password is MD5(password), rehash to bcrypt
            if (md5($plain) === $user->password) {
                $user->password = Hash::make($plain);
                $user->save();
            } else {
                return $invalidResponse();
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        // clear attempts after successful login
        RateLimiter::clear($throttleKey);

        TbLogAktivitas::catat($user->id_user, 'Login ke sistem sebagai ' . ucfirst($user->role));

        return match($user->role) {
            'admin'   => redirect()->route('admin.registrasi.index'),
            'petugas' => redirect()->route('petugas.transaksi.index'),
            'owner'   => redirect()->route('owner.rekap.index'),
            default   => redirect('/'),
        };
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            TbLogAktivitas::catat(Auth::id(), 'Logout dari sistem');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
