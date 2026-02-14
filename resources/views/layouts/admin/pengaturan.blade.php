<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>MIRAI Admin Dashboard - Pengaturan Admin &amp; Sistem</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style type="text/tailwindcss">
        @layer base {
            body {
                font-family: 'Inter', sans-serif;
            }
        }
        :root {
            --primary: #E35D6A;
            --accent-peach: #FFB7A5;
            --sidebar-pink: #FFF0F1;
            --main-bg: #FFFFFF;
        }
    </style>
<script id="tailwind-config">
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                "primary": "#E35D6A",
                "accent-peach": "#FFB7A5",
                "sidebar-pink": "#FFF0F1",
              },
              borderRadius: {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "1rem",
                "2xl": "1.5rem",
              },
            },
          },
        }
    </script>
</head>
<body class="bg-white text-slate-800">
<div class="flex h-screen overflow-hidden">
<aside class="w-72 flex-shrink-0 bg-sidebar-pink flex flex-col border-r border-rose-100">
<div class="p-8 flex items-center gap-3">
<div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-md shadow-primary/20">
<span class="material-icons-round text-white text-2xl">auto_awesome</span>
</div>
<h1 class="text-2xl font-bold tracking-tight text-primary">MIRAI</h1>
</div>
<nav class="flex-1 px-6 space-y-2 overflow-y-auto">
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
<span class="material-symbols-outlined">dashboard</span>
<span class="font-medium">Dashboard</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
<span class="material-symbols-outlined">person</span>
<span class="font-medium">Data Pengguna</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
<span class="material-symbols-outlined">calendar_month</span>
<span class="font-medium">Data Siklus Menstruasi</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
<span class="material-symbols-outlined">health_and_safety</span>
<span class="font-medium">Prediksi &amp; Kesuburan</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
<span class="material-symbols-outlined">analytics</span>
<span class="font-medium">Analitik &amp; Grafik</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
<span class="material-symbols-outlined">description</span>
<span class="font-medium">Laporan</span>
</a>
</nav>
<div class="p-6 border-t border-rose-100 space-y-2">
<a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl transition-all shadow-sm" href="#">
<span class="material-symbols-outlined">settings</span>
<span class="font-medium">Pengaturan</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-rose-500 hover:bg-rose-100 rounded-xl transition-all" href="#">
<span class="material-symbols-outlined">logout</span>
<span class="font-medium">Keluar</span>
</a>
</div>
</aside>
<div class="flex-1 flex flex-col min-w-0">
<header class="h-20 bg-white border-b border-rose-50 flex items-center justify-between px-10 flex-shrink-0">
<div class="flex items-center gap-4">
<h2 class="text-xl font-semibold text-slate-800">Pengaturan Admin &amp; Sistem</h2>
</div>
<div class="flex items-center gap-6">
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
<input class="pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 w-64" placeholder="Cari pengaturan..." type="text"/>
</div>
<button class="p-2 text-slate-400 hover:text-primary relative">
<span class="material-symbols-outlined">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full border-2 border-white"></span>
</button>
<div class="h-10 w-[1px] bg-rose-100"></div>
<div class="flex items-center gap-3">
<div class="text-right hidden sm:block">
<p class="text-sm font-bold text-slate-800">Sarah Jenkins</p>
<p class="text-[11px] text-slate-400 font-medium">Administrator Utama</p>
</div>
<div class="w-10 h-10 rounded-full border-2 border-primary overflow-hidden">
<img alt="Admin Profile" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBd5O7bCkEck3mAE5X76r1tzFmTi7vKs91dKvImYSOPIZA6ffLRGC-STIkifTXUM4dKJ9Nvbv-zKUBcIhJS77ZU5eq6mfSxCy9V3P37-VgdAB4HStRt-tHkMDuxTXsSe8QJZPEJk9yiTrOOa_RBPQ97S-m-R8PDN0JC77F2SEUSSf8FGtzZsGatzd963VH6Zt8vP_uYyN737GksaTEQtNYrn0XDnRS_nOBBStzyRCHfmx8K9P9UxcAPPj9aSLEa-G6uVC8w_qVHKdzY"/>
</div>
</div>
</div>
</header>
<main class="flex-1 overflow-y-auto bg-white p-10">
<div class="max-w-5xl mx-auto">
<div class="flex gap-8 border-b border-rose-100 mb-8">
<button class="pb-4 px-2 border-b-2 border-primary text-primary font-bold text-sm transition-all">Profil Admin</button>
<button class="pb-4 px-2 border-b-2 border-transparent text-slate-400 hover:text-primary font-medium text-sm transition-all">Keamanan</button>
<button class="pb-4 px-2 border-b-2 border-transparent text-slate-400 hover:text-primary font-medium text-sm transition-all">Konfigurasi Sistem</button>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
<div class="lg:col-span-2 space-y-8">
<section>
<h3 class="text-lg font-bold text-slate-800 mb-6">Informasi Personal</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
<input class="w-full border-rose-100 bg-rose-50/20 rounded-xl focus:ring-primary focus:border-primary text-sm p-3" type="text" value="Sarah Jenkins"/>
</div>
<div class="space-y-2">
<label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Email</label>
<input class="w-full border-rose-100 bg-rose-50/20 rounded-xl focus:ring-primary focus:border-primary text-sm p-3" type="email" value="sarah.jenkins@mirai-health.com"/>
</div>
<div class="space-y-2">
<label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor Telepon</label>
<input class="w-full border-rose-100 bg-rose-50/20 rounded-xl focus:ring-primary focus:border-primary text-sm p-3" type="text" value="+62 812 3456 7890"/>
</div>
<div class="space-y-2">
<label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Role Jabatan</label>
<input class="w-full border-rose-50 bg-slate-50 rounded-xl text-slate-400 text-sm p-3 cursor-not-allowed" readonly="" type="text" value="Senior System Administrator"/>
</div>
</div>
</section>
<section>
<h3 class="text-lg font-bold text-slate-800 mb-6">Preferensi Notifikasi</h3>
<div class="space-y-4">
<label class="flex items-center justify-between p-4 bg-rose-50/30 border border-rose-100 rounded-xl cursor-pointer hover:bg-rose-50/50 transition-all">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary">mail</span>
<span class="text-sm font-medium text-slate-700">Laporan Mingguan via Email</span>
</div>
<input checked="" class="w-10 h-5 rounded-full text-primary focus:ring-primary/20 border-rose-200" type="checkbox"/>
</label>
<label class="flex items-center justify-between p-4 bg-rose-50/30 border border-rose-100 rounded-xl cursor-pointer hover:bg-rose-50/50 transition-all">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary">warning</span>
<span class="text-sm font-medium text-slate-700">Peringatan Kegagalan Sistem Real-time</span>
</div>
<input checked="" class="w-10 h-5 rounded-full text-primary focus:ring-primary/20 border-rose-200" type="checkbox"/>
</label>
</div>
</section>
<div class="pt-6 flex gap-4">
<button class="bg-primary text-white px-8 py-3 rounded-xl font-bold text-sm shadow-md shadow-primary/20 hover:bg-rose-600 transition-all">
                                Simpan Perubahan
                            </button>
<button class="bg-white text-slate-500 border border-slate-200 px-8 py-3 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                                Batalkan
                            </button>
</div>
</div>
<div class="space-y-8">
<div class="bg-sidebar-pink/50 border border-rose-100 p-8 rounded-2xl text-center">
<div class="relative w-32 h-32 mx-auto mb-6">
<img alt="Admin Profile Big" class="w-full h-full object-cover rounded-3xl border-4 border-white shadow-lg" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBd5O7bCkEck3mAE5X76r1tzFmTi7vKs91dKvImYSOPIZA6ffLRGC-STIkifTXUM4dKJ9Nvbv-zKUBcIhJS77ZU5eq6mfSxCy9V3P37-VgdAB4HStRt-tHkMDuxTXsSe8QJZPEJk9yiTrOOa_RBPQ97S-m-R8PDN0JC77F2SEUSSf8FGtzZsGatzd963VH6Zt8vP_uYyN737GksaTEQtNYrn0XDnRS_nOBBStzyRCHfmx8K9P9UxcAPPj9aSLEa-G6uVC8w_qVHKdzY"/>
<button class="absolute -bottom-2 -right-2 bg-primary text-white p-2 rounded-xl shadow-lg border-2 border-white hover:scale-105 transition-transform">
<span class="material-symbols-outlined text-sm">photo_camera</span>
</button>
</div>
<h4 class="text-xl font-bold text-slate-800">Sarah Jenkins</h4>
<p class="text-slate-500 text-sm mb-6">ID: ADM-9921-X</p>
<div class="flex flex-col gap-2">
<div class="flex justify-between text-xs py-2 border-b border-rose-100">
<span class="text-slate-400 font-medium">Status Akun</span>
<span class="text-emerald-600 font-bold uppercase tracking-wider">Aktif</span>
</div>
<div class="flex justify-between text-xs py-2 border-b border-rose-100">
<span class="text-slate-400 font-medium">Login Terakhir</span>
<span class="text-slate-600 font-bold">Hari ini, 07:45 WIB</span>
</div>
</div>
</div>
<div class="bg-white border border-rose-100 p-8 rounded-2xl shadow-sm">
<h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-lg">info</span>
                                Informasi Sistem
                            </h4>
<div class="space-y-4">
<div>
<p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Versi Backend</p>
<p class="text-sm font-semibold text-slate-700">v2.4.8-stable</p>
</div>
<div>
<p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Waktu Server</p>
<p class="text-sm font-semibold text-slate-700">GMT+07:00 (Asia/Jakarta)</p>
</div>
<div class="pt-4 border-t border-rose-50">
<button class="text-primary text-xs font-bold flex items-center gap-1 hover:underline">
<span class="material-symbols-outlined text-sm">update</span>
                                        Periksa Pembaruan Sistem
                                    </button>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
</div>
</div>

</body></html>
