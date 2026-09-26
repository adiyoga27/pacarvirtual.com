<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:100'],
            'password' => ['required'],
        ], [
            'login.required' => 'Username / email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');
        $login = trim($data['login']);

        // Bisa login pakai username ATAU email
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$field => $login, 'password' => $data['password']];

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['login' => 'Akun ini bukan admin.'])->onlyInput('login');
            }
            $user = Auth::user();
            $user->forceFill(['last_login_at' => now()])->save();
            ActivityLog::record('login', 'auth', $user->name . ' login ke panel admin');

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        // Fallback: kalau ketik username tapi ternyata email-nya cocok (atau sebaliknya)
        $altField = $field === 'email' ? 'username' : 'email';
        $user = User::where($altField, $login)->first();
        if ($user && Auth::attempt([$altField => $login, 'password' => $data['password']], $remember)) {
            $request->session()->regenerate();
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['login' => 'Akun ini bukan admin.'])->onlyInput('login');
            }
            $user = Auth::user();
            $user->forceFill(['last_login_at' => now()])->save();
            ActivityLog::record('login', 'auth', $user->name . ' login ke panel admin');

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        ActivityLog::record('login', 'auth', 'Gagal login: ' . $login);

        return back()->withErrors(['login' => 'Username/email atau password salah.'])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            ActivityLog::record('logout', 'auth', auth()->user()->name . ' keluar dari panel admin');
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('success', 'Berhasil keluar.');
    }
}
