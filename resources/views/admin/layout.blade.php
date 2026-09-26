<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - PacarVirtual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { 500: '#ff4d6d', 600: '#e63e5c' } } } }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        .sidebar-link.active { background: linear-gradient(90deg, #ff4d6d, #ff758f); color: #fff; }
    </style>
    @stack('head')
</head>
<body class="bg-slate-100 text-slate-800" x-data="{ sidebarOpen: false }">
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-slate-200 transform transition-transform lg:translate-x-0 lg:static lg:flex-shrink-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="h-16 flex items-center gap-3 px-5 border-b border-white/10">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-pink-400 flex items-center justify-center font-black text-white">PV</div>
            <div>
                <div class="font-bold text-white leading-tight">PacarVirtual</div>
                <div class="text-[11px] text-slate-400">Admin Panel</div>
            </div>
        </div>
        <nav class="p-4 space-y-1 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <div class="pt-3 pb-1 px-3 text-[11px] uppercase tracking-wider text-slate-500">Konten Dinamis</div>
            <a href="{{ route('admin.pages.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-richtext"></i> Halaman & Tombol
            </a>
            <a href="{{ route('admin.settings.index', 'appearance') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="bi bi-palette-fill"></i> Tampilan & SEO
            </a>
            <div class="pt-3 pb-1 px-3 text-[11px] uppercase tracking-wider text-slate-500">Penjualan</div>
            <a href="{{ route('admin.orders.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Data Order
            </a>
            <a href="{{ route('admin.reports.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> Laporan Omzet
            </a>
            <a href="{{ route('admin.services.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i> Layanan
            </a>
            <a href="{{ route('admin.payment-methods.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card-2-front"></i> Metode Pembayaran
            </a>
            <a href="{{ route('admin.talents.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.talents.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Talent
            </a>
            <div class="pt-3 pb-1 px-3 text-[11px] uppercase tracking-wider text-slate-500">Sistem</div>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Admin
            </a>
            <a href="{{ route('admin.activity.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Log Sistem
            </a>
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10">
                <i class="bi bi-globe"></i> Lihat Website
            </a>
        </nav>
        <div class="absolute bottom-0 inset-x-0 p-4 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center font-bold text-white">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf
                    <button class="text-slate-400 hover:text-white" title="Keluar"><i class="bi bi-box-arrow-right text-lg"></i></button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 min-w-0">
        <header class="sticky top-0 z-30 h-16 bg-white/80 backdrop-blur border-b flex items-center gap-3 px-4 lg:px-8">
            <button class="lg:hidden p-2 rounded-lg hover:bg-slate-100" @click="sidebarOpen = !sidebarOpen"><i class="bi bi-list text-xl"></i></button>
            <div>
                <h1 class="font-bold text-lg leading-tight">@yield('header', 'Dashboard')</h1>
                <p class="text-xs text-slate-500">@yield('subheader', 'Kelola semua konten dinamis PacarVirtual')</p>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <span class="hidden sm:inline text-xs text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</span>
                <a href="{{ route('home') }}" target="_blank" class="px-3 py-2 text-xs font-semibold rounded-xl bg-slate-900 text-white hover:bg-slate-700"><i class="bi bi-eye"></i> Preview</a>
            </div>
        </header>

        <main class="p-4 lg:p-8">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                    <ul class="list-disc ml-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
