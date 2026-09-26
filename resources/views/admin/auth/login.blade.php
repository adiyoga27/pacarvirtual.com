<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - PacarVirtual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="min-h-screen bg-slate-950 flex items-center justify-center p-4" style="background: radial-gradient(1000px 500px at 20% 10%, #ff4d6d33, transparent), radial-gradient(800px 500px at 90% 90%, #7597de33, transparent), #020617;">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl p-8 shadow-2xl">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-400 flex items-center justify-center font-black text-white text-xl mb-4">PV</div>
            <h1 class="text-2xl font-black">Login Admin</h1>
            <p class="text-sm text-slate-500 mb-6">PacarVirtual CMS — semua konten dinamis</p>
            @if(session('success'))<div class="mb-4 p-3 rounded-xl bg-emerald-50 text-emerald-700 text-sm">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="mb-4 p-3 rounded-xl bg-rose-50 text-rose-700 text-sm">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-slate-600">USERNAME / EMAIL</label>
                    <input name="login" value="{{ old('login', 'admin') }}" required class="mt-1 w-full px-4 py-3 rounded-2xl border focus:ring-2 focus:ring-rose-400 outline-none" placeholder="username atau email">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">PASSWORD</label>
                    <input type="password" name="password" required class="mt-1 w-full px-4 py-3 rounded-2xl border focus:ring-2 focus:ring-rose-400 outline-none" placeholder="••••••••">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" class="rounded"> Ingat saya</label>
                <button class="w-full py-3 rounded-2xl bg-gradient-to-r from-rose-500 to-pink-500 text-white font-bold hover:opacity-90">Masuk Dashboard <i class="bi bi-arrow-right"></i></button>
            </form>
            <p class="text-xs text-slate-400 mt-6 text-center">Bisa pakai username atau email</p>
        </div>
    </div>
</body>
</html>
