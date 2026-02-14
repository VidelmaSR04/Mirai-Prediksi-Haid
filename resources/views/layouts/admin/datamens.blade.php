<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>MIRAI Admin Dashboard - Data Siklus Menstruasi</title>
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
<a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl transition-all shadow-sm" href="#">
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
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
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
<h2 class="text-xl font-semibold text-slate-800">Data Siklus Menstruasi</h2>
</div>
<div class="flex items-center gap-6">
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
<input class="pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 w-64" placeholder="Cari data siklus..." type="text"/>
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
<main class="flex-1 overflow-y-auto bg-white p-10 space-y-10">
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
<div class="bg-white p-8 rounded-2xl border border-rose-100 shadow-sm">
<h4 class="text-lg font-bold text-slate-800 mb-6">Volume Prediksi</h4>
<div class="flex items-end justify-around gap-2 px-2 h-48">
<div class="flex flex-col items-center flex-1">
<span class="text-[11px] font-bold text-primary mb-2">9,420</span>
<div class="w-full max-w-[32px] bg-rose-50 rounded-t-lg relative h-32">
<div class="absolute bottom-0 w-full bg-[#E35D6A] rounded-t-lg" style="height: 60%;"></div>
</div>
<span class="mt-3 text-[10px] text-slate-500 font-bold tracking-tight">FOLIK</span>
</div>
<div class="flex flex-col items-center flex-1">
<span class="text-[11px] font-bold text-primary mb-2">14,200</span>
<div class="w-full max-w-[32px] bg-rose-50 rounded-t-lg relative h-32">
<div class="absolute bottom-0 w-full bg-[#E35D6A] rounded-t-lg" style="height: 90%;"></div>
</div>
<span class="mt-3 text-[10px] text-slate-500 font-bold tracking-tight">OVUL</span>
</div>
<div class="flex flex-col items-center flex-1">
<span class="text-[11px] font-bold text-primary mb-2">11,150</span>
<div class="w-full max-w-[32px] bg-rose-50 rounded-t-lg relative h-32">
<div class="absolute bottom-0 w-full bg-[#E35D6A] rounded-t-lg" style="height: 75%;"></div>
</div>
<span class="mt-3 text-[10px] text-slate-500 font-bold tracking-tight">LUT</span>
</div>
<div class="flex flex-col items-center flex-1">
<span class="text-[11px] font-bold text-primary mb-2">4,130</span>
<div class="w-full max-w-[32px] bg-rose-50 rounded-t-lg relative h-32">
<div class="absolute bottom-0 w-full bg-[#E35D6A] rounded-t-lg" style="height: 35%;"></div>
</div>
<span class="mt-3 text-[10px] text-slate-500 font-bold tracking-tight">MENS</span>
</div>
</div>
</div>
<div class="lg:col-span-2 bg-accent-peach/10 p-8 rounded-2xl border border-accent-peach/20 flex flex-col justify-center">
<div class="grid grid-cols-2 gap-8">
<div>
<p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Rata-rata Panjang Siklus</p>
<h3 class="text-4xl font-bold mt-2 text-primary">28.5 <span class="text-lg font-medium text-slate-400">Hari</span></h3>
<p class="mt-2 text-sm text-emerald-600 flex items-center gap-1 font-semibold">
<span class="material-symbols-outlined text-sm">trending_down</span> -0.2 dari bulan lalu
                            </p>
</div>
<div>
<p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Persentase Normal</p>
<h3 class="text-4xl font-bold mt-2 text-slate-800">84.2%</h3>
<div class="w-full bg-white rounded-full h-2 mt-4 overflow-hidden border border-rose-100">
<div class="bg-primary h-full rounded-full" style="width: 84.2%"></div>
</div>
</div>
</div>
</div>
</section>
<section class="bg-white border border-rose-100 rounded-2xl overflow-hidden shadow-sm">
<div class="p-8 flex items-center justify-between border-b border-rose-50">
<div>
<h4 class="text-lg font-bold text-slate-800">Catatan Siklus Pengguna</h4>
<p class="text-slate-400 text-sm">Menampilkan entri data siklus terbaru dari seluruh pengguna</p>
</div>
<div class="flex gap-3">
<button class="flex items-center gap-2 px-4 py-2 border border-rose-100 rounded-lg text-sm font-bold text-slate-600 hover:bg-rose-50 transition-colors">
<span class="material-symbols-outlined text-lg">filter_list</span>
                            Filter
                        </button>
<button class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold shadow-sm hover:bg-primary/90 transition-colors">
<span class="material-symbols-outlined text-lg">file_download</span>
                            Ekspor CSV
                        </button>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-rose-50/30 text-[11px] uppercase tracking-widest text-slate-500 font-bold">
<tr>
<th class="px-8 py-5">Nama Pengguna</th>
<th class="px-8 py-5">Tanggal Mulai</th>
<th class="px-8 py-5">Tanggal Selesai</th>
<th class="px-8 py-5 text-center">Panjang Siklus (hari)</th>
<th class="px-8 py-5">Pattern</th>
<th class="px-8 py-5"></th>
</tr>
</thead>
<tbody class="divide-y divide-rose-50 text-sm">
<tr class="hover:bg-rose-50/10 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-primary font-bold text-xs">AM</div>
<span class="font-semibold text-slate-700">Amanda Monica</span>
</div>
</td>
<td class="px-8 py-5 text-slate-600">12 Okt 2023</td>
<td class="px-8 py-5 text-slate-600">18 Okt 2023</td>
<td class="px-8 py-5 text-center font-medium">28</td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase rounded-full border border-emerald-100">Normal</span>
</td>
<td class="px-8 py-5 text-right">
<button class="text-slate-400 hover:text-primary"><span class="material-icons-round">more_vert</span></button>
</td>
</tr>
<tr class="hover:bg-rose-50/10 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-primary font-bold text-xs">BP</div>
<span class="font-semibold text-slate-700">Bella Putri</span>
</div>
</td>
<td class="px-8 py-5 text-slate-600">08 Okt 2023</td>
<td class="px-8 py-5 text-slate-600">14 Okt 2023</td>
<td class="px-8 py-5 text-center font-medium">32</td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-rose-50 text-primary text-[10px] font-bold uppercase rounded-full border border-rose-100">Irregular</span>
</td>
<td class="px-8 py-5 text-right">
<button class="text-slate-400 hover:text-primary"><span class="material-icons-round">more_vert</span></button>
</td>
</tr>
<tr class="hover:bg-rose-50/10 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-primary font-bold text-xs">CP</div>
<span class="font-semibold text-slate-700">Citra Permata</span>
</div>
</td>
<td class="px-8 py-5 text-slate-600">15 Okt 2023</td>
<td class="px-8 py-5 text-slate-600">20 Okt 2023</td>
<td class="px-8 py-5 text-center font-medium">29</td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase rounded-full border border-emerald-100">Normal</span>
</td>
<td class="px-8 py-5 text-right">
<button class="text-slate-400 hover:text-primary"><span class="material-icons-round">more_vert</span></button>
</td>
</tr>
<tr class="hover:bg-rose-50/10 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-primary font-bold text-xs">DS</div>
<span class="font-semibold text-slate-700">Diana Sari</span>
</div>
</td>
<td class="px-8 py-5 text-slate-600">10 Okt 2023</td>
<td class="px-8 py-5 text-slate-600">16 Okt 2023</td>
<td class="px-8 py-5 text-center font-medium">27</td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase rounded-full border border-emerald-100">Normal</span>
</td>
<td class="px-8 py-5 text-right">
<button class="text-slate-400 hover:text-primary"><span class="material-icons-round">more_vert</span></button>
</td>
</tr>
<tr class="hover:bg-rose-50/10 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-primary font-bold text-xs">EL</div>
<span class="font-semibold text-slate-700">Eka Lestari</span>
</div>
</td>
<td class="px-8 py-5 text-slate-600">01 Okt 2023</td>
<td class="px-8 py-5 text-slate-600">07 Okt 2023</td>
<td class="px-8 py-5 text-center font-medium">35</td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-rose-50 text-primary text-[10px] font-bold uppercase rounded-full border border-rose-100">Irregular</span>
</td>
<td class="px-8 py-5 text-right">
<button class="text-slate-400 hover:text-primary"><span class="material-icons-round">more_vert</span></button>
</td>
</tr>
</tbody>
</table>
</div>
<div class="px-8 py-6 flex items-center justify-between border-t border-rose-50">
<p class="text-sm text-slate-400">Menampilkan <span class="font-semibold text-slate-700">1-5</span> dari <span class="font-semibold text-slate-700">45,200</span> data</p>
<div class="flex gap-2">
<button class="p-2 border border-rose-50 rounded-lg hover:bg-rose-50 text-slate-400">
<span class="material-symbols-outlined">chevron_left</span>
</button>
<button class="w-8 h-8 bg-primary text-white rounded-lg text-sm font-bold">1</button>
<button class="w-8 h-8 hover:bg-rose-50 text-slate-600 rounded-lg text-sm font-bold">2</button>
<button class="w-8 h-8 hover:bg-rose-50 text-slate-600 rounded-lg text-sm font-bold">3</button>
<button class="p-2 border border-rose-50 rounded-lg hover:bg-rose-50 text-slate-400">
<span class="material-symbols-outlined">chevron_right</span>
</button>
</div>
</div>
</section>
</main>
</div>
</div>

</body></html>
