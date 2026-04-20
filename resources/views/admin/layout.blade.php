<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MIRAI Admin – @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary":      "#E35D6A",
                        "accent-peach": "#FFB7A5",
                        "sidebar-pink": "#FFF0F1",
                        "blush":        "#FDF2F3",
                    },
                    fontFamily: { "sans": ["Plus Jakarta Sans", "sans-serif"] },
                    borderRadius: {
                        "DEFAULT": "0.25rem", "lg": "0.5rem",
                        "xl": "1rem", "2xl": "1.5rem", "3xl": "2rem",
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        #toast { transition: all .3s; transform: translateY(20px); opacity: 0; pointer-events: none; }
        #toast.show { transform: translateY(0); opacity: 1; }
        .fade-in { opacity: 0; animation: fadeIn .45s ease forwards; }
        @keyframes fadeIn { to { opacity: 1; } }
    </style>
    @stack('styles')
</head>
<body class="bg-white text-slate-800 overflow-hidden">
<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ── --}}
    <aside class="w-72 flex-shrink-0 bg-sidebar-pink flex flex-col border-r border-rose-100">

        <div class="p-8 flex items-center gap-3">
            <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-md shadow-primary/20">
                <span class="material-icons-round text-white text-2xl">auto_awesome</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-primary">MIRAI</h1>
        </div>

        <nav class="flex-1 px-6 space-y-2 overflow-y-auto">
            @php
                $menu = [
                    ['route' => 'admin.dashboard', 'icon' => 'dashboard',        'label' => 'Dashboard'],
                    ['route' => 'admin.pengguna',  'icon' => 'person',           'label' => 'Data Pengguna'],
                    ['route' => 'admin.siklus',    'icon' => 'calendar_month',   'label' => 'Data Siklus Menstruasi'],
                    ['route' => 'admin.prediksi',  'icon' => 'health_and_safety','label' => 'Prediksi & Kesuburan'],
                    ['route' => 'admin.analitik',  'icon' => 'analytics',        'label' => 'Analitik & Grafik'],
                    ['route' => 'admin.laporan',   'icon' => 'description',      'label' => 'Laporan'],
                ];
            @endphp

            @foreach($menu as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all
                          {{ $active ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:bg-rose-100 hover:text-primary' }}">
                    <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                    <span class="font-medium">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="p-6 border-t border-rose-100 space-y-2">
            <a href="{{ route('admin.pengaturan') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all
                      {{ request()->routeIs('admin.pengaturan') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:bg-rose-100 hover:text-primary' }}">
                <span class="material-symbols-outlined">settings</span>
                <span class="font-medium">Pengaturan</span>
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-rose-500 hover:bg-rose-100 rounded-xl transition-all">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-medium">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN ── --}}
    <div class="flex-1 flex flex-col min-w-0 h-full">

        {{-- HEADER --}}
        <header class="h-20 bg-white border-b border-rose-50 flex items-center justify-between px-10 flex-shrink-0">
            <h2 class="text-xl font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h2>
            <div class="flex items-center gap-6">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input class="pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 w-64"
                           placeholder="@yield('search-placeholder', 'Cari...')" type="text"/>
                </div>
                <button class="p-2 text-slate-400 hover:text-primary relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full border-2 border-white"></span>
                </button>
                <div class="h-10 w-[1px] bg-rose-100"></div>
                @php $admin = auth('admin')->user(); @endphp
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800">{{ $admin->name ?? 'Admin' }}</p>
                        <p class="text-[11px] text-slate-400 font-medium">Administrator</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm border-2 border-primary">
                        {{ strtoupper(substr($admin->name ?? 'A', 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto bg-blush p-8">
            @if(session('success'))
                <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl text-sm font-semibold">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-xl text-sm font-semibold">
                    <span class="material-symbols-outlined text-primary">error</span>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<div id="toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 bg-slate-800 text-white px-5 py-3 rounded-2xl shadow-2xl text-sm font-semibold">
    <span class="material-symbols-outlined text-emerald-400" id="toastIcon">check_circle</span>
    <span id="toastMsg">Berhasil</span>
</div>
<script>
function showToast(icon, msg, color = 'text-emerald-400') {
    const t = document.getElementById('toast');
    document.getElementById('toastIcon').textContent = icon;
    document.getElementById('toastIcon').className = `material-symbols-outlined ${color}`;
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}
</script>
@stack('scripts')
</body>
</html>
