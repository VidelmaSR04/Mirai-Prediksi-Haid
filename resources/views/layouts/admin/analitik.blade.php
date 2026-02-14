<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>MIRAI Admin Dashboard - Analitik & Grafik Sistem</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          "primary": "#E35D6A",
          "accent-peach": "#FFB7A5",
          "sidebar-pink": "#FFF0F1",
          "blush": "#FDF2F3",
        },
        fontFamily: {
          "sans": ["Plus Jakarta Sans", "sans-serif"],
        },
        borderRadius: {
          "DEFAULT": "0.25rem",
          "lg": "0.5rem",
          "xl": "1rem",
          "2xl": "1.5rem",
          "3xl": "2rem",
        },
      },
    },
  }
</script>
<style>
  body { font-family: 'Plus Jakarta Sans', sans-serif; }
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }

  /* Line chart path animation */
  .line-path {
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
    animation: drawLine 1.6s cubic-bezier(.4,0,.2,1) forwards .3s;
  }
  @keyframes drawLine {
    to { stroke-dashoffset: 0; }
  }

  /* Bar animation */
  .bar-anim {
    animation: growUp .9s cubic-bezier(.4,0,.2,1) forwards;
    transform-origin: bottom;
  }
  @keyframes growUp {
    from { transform: scaleY(0); }
    to   { transform: scaleY(1); }
  }

  /* Fade stagger */
  .fade-in { opacity: 0; animation: fadeIn .5s ease forwards; }
  @keyframes fadeIn { to { opacity: 1; } }
  .d1{animation-delay:.05s} .d2{animation-delay:.1s}
  .d3{animation-delay:.15s} .d4{animation-delay:.2s}
  .d5{animation-delay:.35s} .d6{animation-delay:.5s}
  .d7{animation-delay:.6s}

  /* Heatmap cell */
  .hm-0  { background: #FFF0F1; }
  .hm-1  { background: #FFD6DA; }
  .hm-2  { background: #FFADB5; }
  .hm-3  { background: #F47C87; }
  .hm-4  { background: #E35D6A; }

  /* Sparkline */
  .sparkline { fill: none; stroke-width: 2; stroke-linecap: round; }
</style>
</head>

<body class="bg-white text-slate-800 overflow-hidden">
<div class="flex h-screen overflow-hidden">

  <!-- ── SIDEBAR ── -->
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
      <a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl transition-all shadow-sm" href="#">
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

  <!-- ── MAIN ── -->
  <div class="flex-1 flex flex-col min-w-0 h-full">

    <!-- HEADER -->
    <header class="h-20 bg-white border-b border-rose-50 flex items-center justify-between px-10 flex-shrink-0">
      <h2 class="text-xl font-semibold text-slate-800">Analitik &amp; Grafik Sistem</h2>
      <div class="flex items-center gap-6">
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
          <input class="pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 w-64" placeholder="Cari data analitik..." type="text"/>
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
            <img alt="Admin" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBd5O7bCkEck3mAE5X76r1tzFmTi7vKs91dKvImYSOPIZA6ffLRGC-STIkifTXUM4dKJ9Nvbv-zKUBcIhJS77ZU5eq6mfSxCy9V3P37-VgdAB4HStRt-tHkMDuxTXsSe8QJZPEJk9yiTrOOa_RBPQ97S-m-R8PDN0JC77F2SEUSSf8FGtzZsGatzd963VH6Zt8vP_uYyN737GksaTEQtNYrn0XDnRS_nOBBStzyRCHfmx8K9P9UxcAPPj9aSLEa-G6uVC8w_qVHKdzY"/>
          </div>
        </div>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 overflow-y-auto bg-blush p-8 space-y-5">

      <!-- ── ROW 1: KPI CARDS ── -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Total Pengguna -->
        <div class="fade-in d1 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:shadow-md transition-all">
          <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
              <span class="material-symbols-outlined text-primary" style="font-size:20px">group</span>
            </div>
            <span class="text-[10px] font-bold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">↑ 12.4%</span>
          </div>
          <p class="text-2xl font-bold text-slate-800">12,450</p>
          <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Total Pengguna</p>
          <!-- Sparkline -->
          <svg viewBox="0 0 80 24" class="w-full mt-3 h-6">
            <polyline class="sparkline" stroke="#E35D6A" points="0,18 13,14 26,16 39,10 52,8 65,5 80,2"/>
            <polyline fill="#E35D6A" fill-opacity=".08" stroke="none"
              points="0,18 13,14 26,16 39,10 52,8 65,5 80,2 80,24 0,24"/>
          </svg>
        </div>

        <!-- Pengguna Aktif Harian -->
        <div class="fade-in d2 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:shadow-md transition-all">
          <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
              <span class="material-symbols-outlined text-amber-500" style="font-size:20px">person_check</span>
            </div>
            <span class="text-[10px] font-bold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">↑ 5.7%</span>
          </div>
          <p class="text-2xl font-bold text-slate-800">3,812</p>
          <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Aktif Harian (DAU)</p>
          <svg viewBox="0 0 80 24" class="w-full mt-3 h-6">
            <polyline class="sparkline" stroke="#F59E0B" points="0,20 13,17 26,19 39,13 52,10 65,12 80,7"/>
            <polyline fill="#F59E0B" fill-opacity=".08" stroke="none"
              points="0,20 13,17 26,19 39,13 52,10 65,12 80,7 80,24 0,24"/>
          </svg>
        </div>

        <!-- Tingkat Retensi -->
        <div class="fade-in d3 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:shadow-md transition-all">
          <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
              <span class="material-symbols-outlined text-violet-500" style="font-size:20px">autorenew</span>
            </div>
            <span class="text-[10px] font-bold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">↑ 2.1%</span>
          </div>
          <p class="text-2xl font-bold text-slate-800">78.5<span class="text-base text-slate-400">%</span></p>
          <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Tingkat Retensi</p>
          <svg viewBox="0 0 80 24" class="w-full mt-3 h-6">
            <polyline class="sparkline" stroke="#7C3AED" points="0,16 13,15 26,14 39,12 52,13 65,10 80,8"/>
            <polyline fill="#7C3AED" fill-opacity=".08" stroke="none"
              points="0,16 13,15 26,14 39,12 52,13 65,10 80,8 80,24 0,24"/>
          </svg>
        </div>

        <!-- Rata-rata Sesi -->
        <div class="fade-in d4 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:shadow-md transition-all">
          <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
              <span class="material-symbols-outlined text-blue-400" style="font-size:20px">timer</span>
            </div>
            <span class="text-[10px] font-bold text-rose-400 bg-rose-50 px-2 py-0.5 rounded-full">↓ 0.3%</span>
          </div>
          <p class="text-2xl font-bold text-slate-800">4<span class="text-base text-slate-400">m</span> 22<span class="text-base text-slate-400">s</span></p>
          <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Rata-rata Sesi</p>
          <svg viewBox="0 0 80 24" class="w-full mt-3 h-6">
            <polyline class="sparkline" stroke="#60A5FA" points="0,8 13,10 26,9 39,11 52,10 65,13 80,12"/>
            <polyline fill="#60A5FA" fill-opacity=".08" stroke="none"
              points="0,8 13,10 26,9 39,11 52,10 65,13 80,12 80,24 0,24"/>
          </svg>
        </div>
      </div>

      <!-- ── ROW 2: LINE CHART + AGE DEMOGRAPHICS ── -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Line chart: Pertumbuhan Pengguna 12 Bulan -->
        <div class="fade-in d5 lg:col-span-2 bg-white rounded-2xl border border-rose-100 shadow-sm p-7">
          <div class="flex items-start justify-between mb-5">
            <div>
              <h3 class="text-base font-bold text-slate-800">Pertumbuhan Pengguna</h3>
              <p class="text-xs text-slate-400 mt-0.5">Tren registrasi & pengguna aktif — 12 bulan terakhir</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
              <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-primary inline-block rounded"></span>Registrasi</span>
              <span class="flex items-center gap-1.5 text-slate-400"><span class="w-3 h-0.5 bg-rose-200 inline-block rounded"></span>Aktif</span>
            </div>
          </div>

          <!-- SVG Line Chart -->
          <div class="relative">
            <!-- Y-axis labels -->
            <div class="absolute left-0 top-0 bottom-6 flex flex-col justify-between text-[9px] text-slate-400 font-semibold pr-1" style="width:28px">
              <span>6k</span><span>5k</span><span>4k</span><span>3k</span><span>2k</span><span>1k</span>
            </div>
            <div class="ml-8">
              <svg viewBox="0 0 480 160" class="w-full" style="height:180px">
                <!-- Grid lines -->
                <line x1="0" y1="0"   x2="480" y2="0"   stroke="#FFF0F1" stroke-width="1"/>
                <line x1="0" y1="32"  x2="480" y2="32"  stroke="#FFF0F1" stroke-width="1"/>
                <line x1="0" y1="64"  x2="480" y2="64"  stroke="#FFF0F1" stroke-width="1"/>
                <line x1="0" y1="96"  x2="480" y2="96"  stroke="#FFF0F1" stroke-width="1"/>
                <line x1="0" y1="128" x2="480" y2="128" stroke="#FFF0F1" stroke-width="1"/>

                <!-- Area fill (Registrasi) -->
                <path fill="#E35D6A" fill-opacity=".07"
                  d="M0,128 L40,112 L80,101 L120,90 L160,79 L200,68 L240,53 L280,43 L320,34 L360,24 L400,17 L440,10 L480,5 L480,160 L0,160 Z"/>

                <!-- Line (Registrasi) -->
                <polyline class="line-path" fill="none" stroke="#E35D6A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                  points="0,128 40,112 80,101 120,90 160,79 200,68 240,53 280,43 320,34 360,24 400,17 440,10 480,5"/>

                <!-- Line (Aktif) -->
                <polyline class="line-path" fill="none" stroke="#FFB7A5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  stroke-dasharray="5 3"
                  points="0,140 40,130 80,122 120,115 160,108 200,101 240,91 280,84 320,78 360,70 400,65 440,60 480,56"/>

                <!-- Dots (Registrasi) -->
                <circle cx="480" cy="5"  r="4" fill="#E35D6A" stroke="white" stroke-width="2"/>
                <circle cx="440" cy="10" r="3" fill="#E35D6A" stroke="white" stroke-width="1.5"/>
              </svg>

              <!-- X-axis labels -->
              <div class="flex justify-between text-[9px] text-slate-400 font-bold uppercase mt-1 px-0">
                <span>Jul</span><span>Agt</span><span>Sep</span><span>Okt</span><span>Nov</span><span>Des</span>
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span><span>Jul</span>
              </div>
            </div>
          </div>

          <!-- Summary row -->
          <div class="mt-4 grid grid-cols-3 gap-3 pt-4 border-t border-rose-50">
            <div class="text-center">
              <p class="text-lg font-bold text-slate-800">+5,100</p>
              <p class="text-[10px] text-slate-400 font-semibold">Registrasi Jun</p>
            </div>
            <div class="text-center border-x border-rose-50">
              <p class="text-lg font-bold text-slate-800">+24.8%</p>
              <p class="text-[10px] text-slate-400 font-semibold">MoM Growth</p>
            </div>
            <div class="text-center">
              <p class="text-lg font-bold text-slate-800">12,450</p>
              <p class="text-[10px] text-slate-400 font-semibold">Total Kumulatif</p>
            </div>
          </div>
        </div>

        <!-- Demografi Usia -->
        <div class="fade-in d6 bg-white rounded-2xl border border-rose-100 shadow-sm p-7 flex flex-col">
          <div class="mb-5">
            <h3 class="text-base font-bold text-slate-800">Demografi Usia</h3>
            <p class="text-xs text-slate-400 mt-0.5">Distribusi kelompok umur pengguna</p>
          </div>

          <div class="flex-1 space-y-3">
            <!-- 18–22 -->
            <div>
              <div class="flex justify-between text-xs mb-1">
                <span class="font-semibold text-slate-600">18–22 thn</span>
                <span class="font-bold text-slate-800">22%</span>
              </div>
              <div class="h-3 bg-rose-50 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-accent-peach to-primary rounded-full bar-anim" style="width:22%"></div>
              </div>
            </div>
            <!-- 23–27 -->
            <div>
              <div class="flex justify-between text-xs mb-1">
                <span class="font-semibold text-slate-600">23–27 thn</span>
                <span class="font-bold text-slate-800">34%</span>
              </div>
              <div class="h-3 bg-rose-50 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-primary to-primary rounded-full bar-anim" style="width:34%"></div>
              </div>
            </div>
            <!-- 28–32 -->
            <div>
              <div class="flex justify-between text-xs mb-1">
                <span class="font-semibold text-slate-600">28–32 thn</span>
                <span class="font-bold text-slate-800">28%</span>
              </div>
              <div class="h-3 bg-rose-50 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-primary to-rose-400 rounded-full bar-anim" style="width:28%"></div>
              </div>
            </div>
            <!-- 33–37 -->
            <div>
              <div class="flex justify-between text-xs mb-1">
                <span class="font-semibold text-slate-600">33–37 thn</span>
                <span class="font-bold text-slate-800">11%</span>
              </div>
              <div class="h-3 bg-rose-50 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-rose-300 to-rose-200 rounded-full bar-anim" style="width:11%"></div>
              </div>
            </div>
            <!-- 38+ -->
            <div>
              <div class="flex justify-between text-xs mb-1">
                <span class="font-semibold text-slate-600">38+ thn</span>
                <span class="font-bold text-slate-800">5%</span>
              </div>
              <div class="h-3 bg-rose-50 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-rose-200 to-rose-100 rounded-full bar-anim" style="width:5%"></div>
              </div>
            </div>
          </div>

          <!-- Insight box -->
          <div class="mt-5 bg-rose-50/60 rounded-xl p-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Segmen Terbesar</p>
            <p class="text-sm font-bold text-slate-800">Usia 23–27 tahun</p>
            <p class="text-[11px] text-slate-500 mt-0.5">4,233 pengguna · 34% dari total</p>
          </div>
        </div>
      </div>

      <!-- ── ROW 3: ACTIVITY HEATMAP + TOP REGIONS ── -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Activity Heatmap (Jam × Hari) -->
        <div class="fade-in d6 lg:col-span-2 bg-white rounded-2xl border border-rose-100 shadow-sm p-7">
          <div class="flex items-start justify-between mb-5">
            <div>
              <h3 class="text-base font-bold text-slate-800">Pola Aktivitas Pengguna</h3>
              <p class="text-xs text-slate-400 mt-0.5">Intensitas penggunaan per hari & jam — minggu ini</p>
            </div>
            <div class="flex items-center gap-1.5 text-[9px] text-slate-400 font-semibold">
              <span>Rendah</span>
              <span class="w-3 h-3 rounded hm-0 border border-rose-100 inline-block"></span>
              <span class="w-3 h-3 rounded hm-1 inline-block"></span>
              <span class="w-3 h-3 rounded hm-2 inline-block"></span>
              <span class="w-3 h-3 rounded hm-3 inline-block"></span>
              <span class="w-3 h-3 rounded hm-4 inline-block"></span>
              <span>Tinggi</span>
            </div>
          </div>

          <!-- Heatmap grid -->
          <div class="overflow-x-auto">
            <div class="min-w-[480px]">
              <!-- Hour labels -->
              <div class="flex ml-10 mb-1">
                <div class="grid gap-1 flex-1" style="grid-template-columns: repeat(12, 1fr)">
                  <span class="text-[8px] text-slate-400 text-center font-semibold">00</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">02</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">04</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">06</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">08</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">10</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">12</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">14</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">16</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">18</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">20</span>
                  <span class="text-[8px] text-slate-400 text-center font-semibold">22</span>
                </div>
              </div>

              <!-- Rows per day -->
              <div id="heatmapGrid" class="space-y-1"></div>
            </div>
          </div>

          <p class="text-[10px] text-slate-400 mt-3 font-medium">
            Jam puncak: <span class="font-bold text-slate-600">08.00–10.00</span> dan <span class="font-bold text-slate-600">20.00–22.00</span> WIB
          </p>
        </div>

        <!-- Top Provinsi -->
        <div class="fade-in d7 bg-white rounded-2xl border border-rose-100 shadow-sm p-7 flex flex-col">
          <div class="mb-5">
            <h3 class="text-base font-bold text-slate-800">Sebaran Wilayah</h3>
            <p class="text-xs text-slate-400 mt-0.5">Top 6 provinsi pengguna aktif</p>
          </div>

          <div class="flex-1 space-y-3" id="regionList"></div>

          <div class="mt-5 pt-4 border-t border-rose-50 flex items-center justify-between">
            <p class="text-xs text-slate-400">34 provinsi terjangkau</p>
            <button class="text-xs font-bold text-primary hover:underline">Lihat semua →</button>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
// ─────────────────────────
// HEATMAP
// ─────────────────────────
const days  = ["Sen","Sel","Rab","Kam","Jum","Sab","Min"];
// 12 cols = 2-jam slot (00,02,04...22)
// level 0-4
const heatData = [
  [0,0,0,0,1,3,4,3,2,2,4,3], // Sen
  [0,0,0,0,1,3,3,2,2,2,3,3], // Sel
  [0,0,0,0,2,4,4,3,3,3,4,4], // Rab
  [0,0,0,0,1,3,3,2,2,2,3,3], // Kam
  [0,0,0,0,1,2,3,2,2,3,4,4], // Jum
  [0,0,0,1,1,2,3,3,3,3,3,2], // Sab
  [0,0,0,1,1,1,2,2,2,2,2,2], // Min
];
const grid = document.getElementById("heatmapGrid");
days.forEach((day, di) => {
  const row = document.createElement("div");
  row.className = "flex items-center gap-1";
  row.innerHTML = `<span class="text-[9px] font-bold text-slate-400 w-8 text-right pr-1">${day}</span>`;
  const cells = document.createElement("div");
  cells.className = "grid gap-1 flex-1";
  cells.style.gridTemplateColumns = "repeat(12, 1fr)";
  heatData[di].forEach(level => {
    const cell = document.createElement("div");
    cell.className = `h-7 rounded hm-${level} transition-all hover:scale-110 cursor-default`;
    cells.appendChild(cell);
  });
  row.appendChild(cells);
  grid.appendChild(row);
});

// ─────────────────────────
// REGION LIST
// ─────────────────────────
const regions = [
  { name:"DKI Jakarta",    count:3204, pct:25.7, color:"#E35D6A" },
  { name:"Jawa Barat",     count:2180, pct:17.5, color:"#F47C87" },
  { name:"Jawa Tengah",    count:1540, pct:12.4, color:"#FFB7A5" },
  { name:"Jawa Timur",     count:1320, pct:10.6, color:"#FBBF24" },
  { name:"Sumatera Utara", count: 890, pct: 7.1, color:"#A78BFA" },
  { name:"Banten",         count: 720, pct: 5.8, color:"#60A5FA" },
];
const rl = document.getElementById("regionList");
regions.forEach((r, i) => {
  rl.innerHTML += `
    <div class="group">
      <div class="flex items-center justify-between mb-1">
        <div class="flex items-center gap-2">
          <span class="text-[10px] font-bold text-slate-400">${String(i+1).padStart(2,'0')}</span>
          <span class="text-xs font-semibold text-slate-700">${r.name}</span>
        </div>
        <span class="text-xs font-bold text-slate-800">${r.count.toLocaleString('id-ID')}</span>
      </div>
      <div class="h-2 bg-rose-50 rounded-full overflow-hidden">
        <div class="h-full rounded-full bar-anim" style="width:${r.pct*3.2}%;background:${r.color}"></div>
      </div>
    </div>`;
});
</script>
</body>
</html>
