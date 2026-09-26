<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $users = User::when($q, fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('username', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")))
            ->orderBy('id')
            ->get();

        $hasLog = Schema::hasTable('activity_logs');
        $recentLogs = $hasLog
            ? ActivityLog::with('user')->orderByDesc('id')->limit(8)->get()
            : collect();
        $logCounts = $hasLog
            ? ActivityLog::selectRaw('user_id, COUNT(*) as c')->groupBy('user_id')->pluck('c', 'user_id')
            : collect();

        return view('admin.users.index', compact('users', 'recentLogs', 'logCounts', 'q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'username' => 'required|alpha_dash|min:3|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);
        $user = User::create([
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);
        ActivityLog::record('create', 'users', 'Tambah admin @' . $user->username . ' (' . $user->email . ')');
        return back()->with('success', 'Admin @' . $user->username . ' ditambah. Bisa login pakai username atau email.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'username' => 'required|alpha_dash|min:3|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);
        $user->name = $data['name'];
        $user->username = strtolower($data['username']);
        $user->email = $data['email'];
        $pwReset = false;
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $pwReset = true;
        }
        $user->save();

        ActivityLog::record('update', 'users', 'Edit admin @' . $user->username . ($pwReset ? ' + reset password' : ''));

        return back()->with('success', 'Admin "' . $user->name . '" diperbarui.' . ($pwReset ? ' Password baru sudah berlaku.' : ''));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        ActivityLog::record('delete', 'users', 'Hapus admin @' . $user->username . ' (' . $user->email . ')');
        $user->delete();
        return back()->with('success', 'Admin dihapus.');
    }
}
