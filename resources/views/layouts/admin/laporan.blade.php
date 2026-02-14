<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>MIRAI Admin Dashboard - Pusat Laporan & Ekspor</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
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
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
  /* Fade in */
  .fade-in { opacity:0; animation: fi .45s ease forwards; }
  @keyframes fi { to { opacity:1; } }
  .d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}
  .d4{animation-delay:.2s}.d5{animation-delay:.3s}.d6{animation-delay:.4s}

  /* Template card selected state */
  .tpl-card { cursor:pointer; transition: all .2s; }
  .tpl-card:hover  { border-color: #E35D6A; box-shadow: 0 0 0 3px rgba(227,93,106,.08); }
  .tpl-card.active { border-color: #E35D6A; box-shadow: 0 0 0 3px rgba(227,93,106,.15); background:#FDF2F3; }

  /* Progress bar pulse */
  @keyframes pulse-bar {
    0%,100% { opacity:1; } 50% { opacity:.6; }
  }
  .processing .prog-bar { animation: pulse-bar 1.4s ease infinite; }

  /* Toast */
  #toast { transition: all .3s; transform: translateY(20px); opacity:0; pointer-events:none; }
  #toast.show { transform: translateY(0); opacity:1; }
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
        <span class="material-symbols-outlined">dashboard</span><span class="font-medium">Dashboard</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
        <span class="material-symbols-outlined">person</span><span class="font-medium">Data Pengguna</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
        <span class="material-symbols-outlined">calendar_month</span><span class="font-medium">Data Siklus Menstruasi</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
        <span class="material-symbols-outlined">health_and_safety</span><span class="font-medium">Prediksi &amp; Kesuburan</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
        <span class="material-symbols-outlined">analytics</span><span class="font-medium">Analitik &amp; Grafik</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl transition-all shadow-sm" href="#">
        <span class="material-symbols-outlined">description</span><span class="font-medium">Laporan</span>
      </a>
    </nav>
    <div class="p-6 border-t border-rose-100 space-y-2">
      <a class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all" href="#">
        <span class="material-symbols-outlined">settings</span><span class="font-medium">Pengaturan</span>
      </a>
      <a class="flex items-center gap-3 px-4 py-3 text-rose-500 hover:bg-rose-100 rounded-xl transition-all" href="#">
        <span class="material-symbols-outlined">logout</span><span class="font-medium">Keluar</span>
      </a>
    </div>
  </aside>

  <!-- ── MAIN ── -->
  <div class="flex-1 flex flex-col min-w-0 h-full">

    <!-- HEADER -->
    <header class="h-20 bg-white border-b border-rose-50 flex items-center justify-between px-10 flex-shrink-0">
      <h2 class="text-xl font-semibold text-slate-800">Pusat Laporan &amp; Ekspor</h2>
      <div class="flex items-center gap-6">
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
          <input class="pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 w-64" placeholder="Cari laporan..." type="text"/>
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

      <!-- ── ROW 1: STAT CARDS ── -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="fade-in d1 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
          <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-primary" style="font-size:20px">description</span>
          </div>
          <p class="text-2xl font-bold text-slate-800">48</p>
          <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Total Laporan</p>
          <p class="text-[10px] text-emerald-500 font-semibold mt-1">+5 bulan ini</p>
        </div>
        <div class="fade-in d2 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-emerald-500" style="font-size:20px">download_done</span>
          </div>
          <p class="text-2xl font-bold text-slate-800">5</p>
          <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Ekspor Hari Ini</p>
          <p class="text-[10px] text-slate-400 font-semibold mt-1">Terakhir 14:20 WIB</p>
        </div>
        <div class="fade-in d3 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
          <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-amber-500" style="font-size:20px">schedule</span>
          </div>
          <p class="text-2xl font-bold text-slate-800">2</p>
          <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Terjadwal Otomatis</p>
          <p class="text-[10px] text-slate-400 font-semibold mt-1">Bulanan & Mingguan</p>
        </div>
        <div class="fade-in d4 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
          <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-blue-400" style="font-size:20px">storage</span>
          </div>
          <p class="text-2xl font-bold text-slate-800">128<span class="text-base text-slate-400 font-semibold"> MB</span></p>
          <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Total Ukuran Arsip</p>
          <p class="text-[10px] text-slate-400 font-semibold mt-1">dari 500 MB kuota</p>
        </div>
      </div>

      <!-- ── ROW 2: KONFIGURASI + TEMPLATE ── -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Template Laporan -->
        <div class="fade-in d4 bg-white rounded-2xl border border-rose-100 shadow-sm p-7">
          <h3 class="text-base font-bold text-slate-800 mb-1">Template Laporan</h3>
          <p class="text-xs text-slate-400 mb-5">Pilih jenis laporan yang ingin dibuat</p>
          <div class="space-y-3" id="templateList">

            <div class="tpl-card active border-2 rounded-xl p-4 flex items-center gap-3" onclick="selectTemplate(this,'Laporan Bulanan Sistem')">
              <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-primary" style="font-size:18px">calendar_month</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800">Laporan Bulanan Sistem</p>
                <p class="text-[10px] text-slate-400">Ringkasan aktivitas & pengguna</p>
              </div>
              <span class="material-symbols-outlined text-primary text-lg check-icon">check_circle</span>
            </div>

            <div class="tpl-card border-2 border-slate-100 rounded-xl p-4 flex items-center gap-3" onclick="selectTemplate(this,'Data Prediksi & Kesuburan')">
              <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-emerald-500" style="font-size:18px">favorite</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800">Data Prediksi & Kesuburan</p>
                <p class="text-[10px] text-slate-400">Analisis siklus & akurasi model</p>
              </div>
              <span class="material-symbols-outlined text-slate-200 text-lg check-icon">check_circle</span>
            </div>

            <div class="tpl-card border-2 border-slate-100 rounded-xl p-4 flex items-center gap-3" onclick="selectTemplate(this,'Demografis Pengguna')">
              <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-violet-500" style="font-size:18px">group</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800">Demografis Pengguna</p>
                <p class="text-[10px] text-slate-400">Usia, lokasi, & retensi</p>
              </div>
              <span class="material-symbols-outlined text-slate-200 text-lg check-icon">check_circle</span>
            </div>

            <div class="tpl-card border-2 border-slate-100 rounded-xl p-4 flex items-center gap-3" onclick="selectTemplate(this,'Performa Algoritma')">
              <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-amber-500" style="font-size:18px">model_training</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800">Performa Algoritma</p>
                <p class="text-[10px] text-slate-400">Akurasi & validasi model AI</p>
              </div>
              <span class="material-symbols-outlined text-slate-200 text-lg check-icon">check_circle</span>
            </div>

          </div>
        </div>

        <!-- Konfigurasi & Generate -->
        <div class="fade-in d5 lg:col-span-2 bg-white rounded-2xl border border-rose-100 shadow-sm p-7 flex flex-col">
          <div class="flex items-start justify-between mb-5">
            <div>
              <h3 class="text-base font-bold text-slate-800">Konfigurasi Laporan</h3>
              <p class="text-xs text-slate-400 mt-0.5">Template: <span id="selectedTplLabel" class="font-semibold text-primary">Laporan Bulanan Sistem</span></p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 flex-1">

            <!-- Rentang Waktu -->
            <div class="space-y-2">
              <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Rentang Waktu</label>
              <div class="flex gap-2">
                <div class="relative flex-1">
                  <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">calendar_today</span>
                  <input type="date" value="2023-11-01" class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-rose-100 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 outline-none"/>
                </div>
                <div class="relative flex-1">
                  <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">event</span>
                  <input type="date" value="2023-11-30" class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-rose-100 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 outline-none"/>
                </div>
              </div>
            </div>

            <!-- Format Ekspor -->
            <div class="space-y-2">
              <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Format Ekspor</label>
              <div class="flex gap-2">
                <button id="fmtPDF" onclick="setFormat('PDF')" class="fmt-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-primary bg-rose-50 text-primary text-xs font-bold transition-all">
                  <span class="material-symbols-outlined" style="font-size:16px">picture_as_pdf</span> PDF
                </button>
                <button id="fmtXLSX" onclick="setFormat('XLSX')" class="fmt-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-slate-100 text-slate-500 text-xs font-bold transition-all hover:border-primary hover:text-primary">
                  <span class="material-symbols-outlined" style="font-size:16px">table_view</span> Excel
                </button>
                <button id="fmtCSV" onclick="setFormat('CSV')" class="fmt-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-slate-100 text-slate-500 text-xs font-bold transition-all hover:border-primary hover:text-primary">
                  <span class="material-symbols-outlined" style="font-size:16px">csv</span> CSV
                </button>
              </div>
            </div>

            <!-- Kolom yang disertakan -->
            <div class="space-y-2 md:col-span-2">
              <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Kolom yang Disertakan</label>
              <div class="flex flex-wrap gap-2">
                <label class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 border border-rose-100 rounded-full cursor-pointer hover:border-primary transition-all">
                  <input type="checkbox" checked class="accent-primary w-3 h-3"/><span class="text-xs font-semibold text-slate-600">Data Pengguna</span>
                </label>
                <label class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 border border-rose-100 rounded-full cursor-pointer hover:border-primary transition-all">
                  <input type="checkbox" checked class="accent-primary w-3 h-3"/><span class="text-xs font-semibold text-slate-600">Siklus Menstruasi</span>
                </label>
                <label class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-full cursor-pointer hover:border-primary transition-all">
                  <input type="checkbox" class="accent-primary w-3 h-3"/><span class="text-xs font-semibold text-slate-500">Prediksi Ovulasi</span>
                </label>
                <label class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 border border-rose-100 rounded-full cursor-pointer hover:border-primary transition-all">
                  <input type="checkbox" checked class="accent-primary w-3 h-3"/><span class="text-xs font-semibold text-slate-600">Analitik Sistem</span>
                </label>
                <label class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-full cursor-pointer hover:border-primary transition-all">
                  <input type="checkbox" class="accent-primary w-3 h-3"/><span class="text-xs font-semibold text-slate-500">Demografi Lokasi</span>
                </label>
              </div>
            </div>

          </div>

          <!-- Progress bar (hidden by default) -->
          <div id="progressSection" class="hidden mt-5 pt-5 border-t border-rose-50">
            <div class="flex items-center justify-between mb-2">
              <p class="text-xs font-semibold text-slate-600" id="progressLabel">Mempersiapkan laporan...</p>
              <p class="text-xs font-bold text-primary" id="progressPct">0%</p>
            </div>
            <div class="h-2 bg-rose-50 rounded-full overflow-hidden">
              <div id="progBar" class="prog-bar h-full bg-gradient-to-r from-primary to-accent-peach rounded-full transition-all duration-500" style="width:0%"></div>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="mt-5 pt-5 border-t border-rose-50 flex items-center gap-3 flex-wrap">
            <button onclick="generateReport()"
              class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary/90 transition-all shadow-md shadow-primary/20">
              <span class="material-symbols-outlined text-lg">download</span>
              Buat &amp; Ekspor Laporan
            </button>
            <button onclick="previewReport()"
              class="flex items-center gap-2 px-5 py-3 border-2 border-rose-100 text-primary rounded-xl font-bold text-sm hover:bg-rose-50 transition-all">
              <span class="material-symbols-outlined text-lg">visibility</span>
              Pratinjau
            </button>
            <button class="flex items-center gap-2 px-5 py-3 border-2 border-slate-100 text-slate-500 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
              <span class="material-symbols-outlined text-lg">schedule_send</span>
              Jadwalkan
            </button>
          </div>
        </div>
      </div>

      <!-- ── ROW 3: RIWAYAT EKSPOR ── -->
      <div class="fade-in d6 bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-rose-50 flex items-center justify-between flex-wrap gap-3">
          <div>
            <h3 class="text-base font-bold text-slate-800">Riwayat Ekspor Laporan</h3>
            <p class="text-xs text-slate-400 mt-0.5">Semua laporan yang telah dibuat dan diunduh</p>
          </div>
          <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 bg-rose-50 text-primary text-[10px] font-bold rounded-full border border-rose-100">5 Ekspor Hari Ini</span>
            <button class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-primary px-3 py-1.5 border border-slate-100 rounded-xl hover:bg-rose-50 transition-all">
              <span class="material-symbols-outlined text-base">filter_list</span> Filter
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-rose-50/30 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-rose-50">
              <tr>
                <th class="px-7 py-4">Nama Laporan</th>
                <th class="px-5 py-4">Template</th>
                <th class="px-5 py-4 text-center">Format</th>
                <th class="px-5 py-4 text-center">Ukuran</th>
                <th class="px-5 py-4">Dibuat Oleh</th>
                <th class="px-5 py-4">Waktu</th>
                <th class="px-5 py-4 text-center">Status</th>
                <th class="px-7 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-rose-50" id="historyTableBody">
            </tbody>
          </table>
        </div>

        <div class="px-7 py-4 bg-rose-50/20 border-t border-rose-50 flex items-center justify-between">
          <p class="text-xs text-slate-400 font-medium">Menampilkan <span class="font-bold text-slate-700">1–5</span> dari <span class="font-bold text-slate-700">48</span> laporan</p>
          <div class="flex items-center gap-2">
            <button class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 text-slate-400 hover:text-primary hover:bg-rose-50 transition-all disabled:opacity-30" disabled>
              <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button class="w-9 h-9 flex items-center justify-center rounded-xl bg-primary text-white font-bold text-sm shadow-md shadow-primary/20">1</button>
            <button class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 text-slate-600 font-bold text-sm hover:bg-rose-50">2</button>
            <button class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 text-slate-600 font-bold text-sm hover:bg-rose-50">3</button>
            <button class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 text-slate-400 hover:text-primary hover:bg-rose-50 transition-all">
              <span class="material-symbols-outlined">chevron_right</span>
            </button>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 bg-slate-800 text-white px-5 py-3 rounded-2xl shadow-2xl text-sm font-semibold">
  <span class="material-symbols-outlined text-emerald-400" id="toastIcon">check_circle</span>
  <span id="toastMsg">Laporan berhasil dibuat!</span>
</div>

<script>
// ─────────────────────────
// HISTORY DATA
// ─────────────────────────
const historyData = [
  { nama:"Laporan_Bulanan_November_2023", template:"Laporan Bulanan Sistem",   fmt:"PDF",  size:"2.4 MB", oleh:"Sarah Jenkins",  waktu:"Tadi, 14:20",         status:"Selesai" },
  { nama:"Analitik_Prediksi_Q4_Final",   template:"Data Prediksi & Kesuburan",fmt:"XLSX", size:"1.1 MB", oleh:"Sarah Jenkins",  waktu:"Kemarin, 09:15",      status:"Selesai" },
  { nama:"Data_Demografi_Pengguna_Global",template:"Demografis Pengguna",      fmt:"PDF",  size:"3.8 MB", oleh:"System (Auto)",  waktu:"12 Nov 2023",         status:"Selesai" },
  { nama:"Siklus_Oktober_Summary",        template:"Laporan Bulanan Sistem",   fmt:"CSV",  size:"540 KB", oleh:"Sarah Jenkins",  waktu:"01 Nov 2023",         status:"Selesai" },
  { nama:"Performa_Model_AI_Q3",          template:"Performa Algoritma",       fmt:"XLSX", size:"890 KB", oleh:"System (Auto)",  waktu:"01 Okt 2023",         status:"Selesai" },
];

function fmtBadge(fmt) {
  const map = {
    "PDF":  "bg-red-50 text-red-500 border-red-100",
    "XLSX": "bg-emerald-50 text-emerald-600 border-emerald-100",
    "CSV":  "bg-blue-50 text-blue-500 border-blue-100",
  };
  return `<span class="px-2.5 py-1 text-[9px] font-bold uppercase rounded-full border ${map[fmt]||'bg-slate-50 text-slate-500'}">${fmt}</span>`;
}

function statusBadge(s) {
  if (s === "Selesai") return `<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-bold rounded-full border border-emerald-100"><span class="material-symbols-outlined" style="font-size:11px">check</span>Selesai</span>`;
  if (s === "Proses")  return `<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-600 text-[9px] font-bold rounded-full border border-amber-100"><span class="material-symbols-outlined" style="font-size:11px">pending</span>Proses</span>`;
  return `<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 text-primary text-[9px] font-bold rounded-full border border-rose-100"><span class="material-symbols-outlined" style="font-size:11px">error</span>Gagal</span>`;
}

function tplIcon(tpl) {
  const map = {
    "Laporan Bulanan Sistem":    { icon:"calendar_month", cls:"bg-rose-100 text-primary" },
    "Data Prediksi & Kesuburan":{ icon:"favorite",       cls:"bg-emerald-50 text-emerald-500" },
    "Demografis Pengguna":       { icon:"group",          cls:"bg-violet-50 text-violet-500" },
    "Performa Algoritma":        { icon:"model_training", cls:"bg-amber-50 text-amber-500" },
  };
  const t = map[tpl] || { icon:"description", cls:"bg-slate-100 text-slate-500" };
  return `<div class="w-8 h-8 rounded-lg ${t.cls} flex items-center justify-center flex-shrink-0">
    <span class="material-symbols-outlined" style="font-size:15px">${t.icon}</span>
  </div>`;
}

function renderHistory() {
  const tbody = document.getElementById("historyTableBody");
  tbody.innerHTML = historyData.map(r => `
    <tr class="hover:bg-rose-50/10 transition-colors">
      <td class="px-7 py-4">
        <div class="flex items-center gap-2.5">
          ${tplIcon(r.template)}
          <span class="text-sm font-semibold text-slate-800">${r.nama}</span>
        </div>
      </td>
      <td class="px-5 py-4">
        <span class="text-xs text-slate-500">${r.template}</span>
      </td>
      <td class="px-5 py-4 text-center">${fmtBadge(r.fmt)}</td>
      <td class="px-5 py-4 text-center">
        <span class="text-xs font-medium text-slate-500">${r.size}</span>
      </td>
      <td class="px-5 py-4">
        <span class="text-xs text-slate-600 font-medium">${r.oleh}</span>
      </td>
      <td class="px-5 py-4">
        <span class="text-xs text-slate-400">${r.waktu}</span>
      </td>
      <td class="px-5 py-4 text-center">${statusBadge(r.status)}</td>
      <td class="px-7 py-4 text-right">
        <div class="flex items-center justify-end gap-1">
          <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-primary transition-colors" title="Unduh">
            <span class="material-symbols-outlined text-base">download</span>
          </button>
          <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="Pratinjau">
            <span class="material-symbols-outlined text-base">visibility</span>
          </button>
          <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-primary transition-colors" title="Hapus">
            <span class="material-symbols-outlined text-base">delete</span>
          </button>
        </div>
      </td>
    </tr>
  `).join("");
}

// ─────────────────────────
// TEMPLATE SELECTION
// ─────────────────────────
function selectTemplate(el, label) {
  document.querySelectorAll(".tpl-card").forEach(c => {
    c.classList.remove("active");
    c.querySelector(".check-icon").style.color = "#E2E8F0";
  });
  el.classList.add("active");
  el.querySelector(".check-icon").style.color = "#E35D6A";
  document.getElementById("selectedTplLabel").textContent = label;
}

// ─────────────────────────
// FORMAT SELECTION
// ─────────────────────────
let selectedFormat = "PDF";
function setFormat(fmt) {
  selectedFormat = fmt;
  document.querySelectorAll(".fmt-btn").forEach(b => {
    b.classList.remove("border-primary","bg-rose-50","text-primary");
    b.classList.add("border-slate-100","text-slate-500");
  });
  const btn = document.getElementById("fmt" + fmt);
  btn.classList.add("border-primary","bg-rose-50","text-primary");
  btn.classList.remove("border-slate-100","text-slate-500");
}

// ─────────────────────────
// GENERATE REPORT (simulasi)
// ─────────────────────────
function generateReport() {
  const sec  = document.getElementById("progressSection");
  const bar  = document.getElementById("progBar");
  const lbl  = document.getElementById("progressLabel");
  const pct  = document.getElementById("progressPct");

  sec.classList.remove("hidden");
  const steps = [
    { p:20,  t:"Mengumpulkan data..." },
    { p:50,  t:"Memproses & memformat..." },
    { p:80,  t:"Menyiapkan file ekspor..." },
    { p:100, t:"Selesai!" },
  ];
  let i = 0;
  const run = () => {
    if (i >= steps.length) {
      showToast("check_circle", "Laporan berhasil dibuat & diunduh!");
      setTimeout(() => sec.classList.add("hidden"), 2000);
      return;
    }
    bar.style.width = steps[i].p + "%";
    lbl.textContent = steps[i].t;
    pct.textContent = steps[i].p + "%";
    i++;
    setTimeout(run, 700);
  };
  run();
}

function previewReport() {
  showToast("visibility", "Membuka pratinjau laporan...");
}

// ─────────────────────────
// TOAST
// ─────────────────────────
function showToast(icon, msg) {
  const t = document.getElementById("toast");
  document.getElementById("toastIcon").textContent = icon;
  document.getElementById("toastMsg").textContent  = msg;
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 3000);
}

// ─────────────────────────
// INIT
// ─────────────────────────
renderHistory();
</script>
</body>
</html>
