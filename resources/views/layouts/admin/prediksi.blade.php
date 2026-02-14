<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>MIRAI Prediksi & Kesuburan - Prediksi & Kesuburan</title>
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
          "soft-mint": "#D1FAE5",
          "blush": "#FDF2F3",
        },
        fontFamily: {
          "sans": ["Plus Jakarta Sans", "sans-serif"],
          "display": ["DM Serif Display", "serif"],
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
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
  body { font-family: 'Plus Jakarta Sans', sans-serif; }

  /* Donut chart */
  .donut-ring { transition: stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1); }

  /* Bar chart animation */
  .bar-fill { animation: growUp 1s cubic-bezier(.4,0,.2,1) forwards; transform-origin: bottom; }
  @keyframes growUp {
    from { transform: scaleY(0); }
    to   { transform: scaleY(1); }
  }

  /* Fade-in stagger */
  .fade-in { opacity: 0; animation: fadeIn .5s ease forwards; }
  @keyframes fadeIn { to { opacity: 1; } }
  .delay-1 { animation-delay: .1s; }
  .delay-2 { animation-delay: .2s; }
  .delay-3 { animation-delay: .3s; }
  .delay-4 { animation-delay: .4s; }
  .delay-5 { animation-delay: .5s; }

  /* Phase pill */
  .phase-menstruasi  { background: #FEE2E2; color: #E35D6A; }
  .phase-folikel     { background: #FEF3C7; color: #D97706; }
  .phase-ovulasi     { background: #D1FAE5; color: #059669; }
  .phase-luteal      { background: #EDE9FE; color: #7C3AED; }
</style>
</head>

<body class="bg-white text-slate-800 overflow-hidden">
<div class="flex h-screen overflow-hidden">

  <!-- ── SIDEBAR (identical to other pages) ── -->
  <aside class="w-72 flex-shrink-0 bg-sidebar-pink flex flex-col border-r border-rose-100">
    <div class="p-8 flex items-center gap-3">
      <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-md shadow-primary/20">
        <span class="material-icons-round text-white text-2xl">auto_awesome</span>
      </div>
      <h1 class="text-2xl font-bold tracking-tight text-primary" style="font-family:'Plus Jakarta Sans'">MIRAI</h1>
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
      <a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl transition-all shadow-sm" href="#">
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

  <!-- ── MAIN ── -->
  <div class="flex-1 flex flex-col min-w-0 h-full">

    <!-- HEADER -->
    <header class="h-20 bg-white border-b border-rose-50 flex items-center justify-between px-10 flex-shrink-0">
      <div class="flex items-center gap-4">
        <h2 class="text-xl font-semibold text-slate-800">Prediksi &amp; Kesuburan</h2>
      </div>
      <div class="flex items-center gap-6">
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
          <input class="pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 w-64" placeholder="Cari data prediksi..." type="text"/>
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
    <main class="flex-1 overflow-y-auto bg-blush p-8 space-y-6">

      <!-- ── SECTION 1: STAT CARDS ── -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Akurasi -->
        <div class="fade-in delay-1 bg-white rounded-2xl border border-rose-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-primary/20 transition-all">
          <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-primary" style="font-size:22px">verified</span>
          </div>
          <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Akurasi Prediksi</p>
            <p class="text-2xl font-bold text-slate-800 leading-tight">98.2<span class="text-sm text-slate-400 font-semibold">%</span></p>
            <p class="text-[10px] text-emerald-500 font-semibold mt-0.5">↑ 0.4% dari bulan lalu</p>
          </div>
        </div>

        <!-- Total Prediksi -->
        <div class="fade-in delay-2 bg-white rounded-2xl border border-rose-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-primary/20 transition-all">
          <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-amber-500" style="font-size:22px">query_stats</span>
          </div>
          <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Prediksi</p>
            <p class="text-2xl font-bold text-slate-800 leading-tight">38,900</p>
            <p class="text-[10px] text-emerald-500 font-semibold mt-0.5">↑ 1,240 minggu ini</p>
          </div>
        </div>

        <!-- Rerata Siklus -->
        <div class="fade-in delay-3 bg-white rounded-2xl border border-rose-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-primary/20 transition-all">
          <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-blue-400" style="font-size:22px">update</span>
          </div>
          <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Rerata Siklus</p>
            <p class="text-2xl font-bold text-slate-800 leading-tight">28.4<span class="text-sm text-slate-400 font-semibold"> hari</span></p>
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Rentang: 21–35 hari</p>
          </div>
        </div>

        <!-- Fase Ovulasi Aktif -->
        <div class="fade-in delay-4 bg-white rounded-2xl border border-rose-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-primary/20 transition-all">
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-emerald-500" style="font-size:22px">favorite</span>
          </div>
          <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Sedang Subur</p>
            <p class="text-2xl font-bold text-slate-800 leading-tight">3,214</p>
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">pengguna hari ini</p>
          </div>
        </div>
      </div>

      <!-- ── SECTION 2: CHARTS ── -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Bar chart: Volume per Fase -->
        <div class="fade-in delay-2 lg:col-span-2 bg-white rounded-2xl border border-rose-100 shadow-sm p-7">
          <div class="flex items-start justify-between mb-6">
            <div>
              <h3 class="text-base font-bold text-slate-800">Distribusi Fase Siklus</h3>
              <p class="text-xs text-slate-400 mt-0.5">Volume prediksi per fase — Oktober 2023</p>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 bg-rose-50 text-primary rounded-full">Bulan Ini</span>
          </div>

          <!-- Bar chart -->
          <div class="flex items-end gap-4 h-44 px-2">
            <!-- Folikel -->
            <div class="flex-1 flex flex-col items-center gap-2">
              <span class="text-xs font-bold text-amber-600">9,420</span>
              <div class="w-full relative bg-amber-50 rounded-xl overflow-hidden" style="height:120px">
                <div class="bar-fill absolute bottom-0 w-full bg-gradient-to-t from-amber-400 to-amber-300 rounded-xl" style="height:60%"></div>
              </div>
              <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Folikel</span>
            </div>
            <!-- Ovulasi -->
            <div class="flex-1 flex flex-col items-center gap-2">
              <span class="text-xs font-bold text-emerald-600">14,200</span>
              <div class="w-full relative bg-emerald-50 rounded-xl overflow-hidden" style="height:120px">
                <div class="bar-fill absolute bottom-0 w-full bg-gradient-to-t from-emerald-500 to-emerald-400 rounded-xl" style="height:100%"></div>
              </div>
              <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Ovulasi</span>
            </div>
            <!-- Luteal -->
            <div class="flex-1 flex flex-col items-center gap-2">
              <span class="text-xs font-bold text-violet-600">11,150</span>
              <div class="w-full relative bg-violet-50 rounded-xl overflow-hidden" style="height:120px">
                <div class="bar-fill absolute bottom-0 w-full bg-gradient-to-t from-violet-400 to-violet-300 rounded-xl" style="height:78%"></div>
              </div>
              <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Luteal</span>
            </div>
            <!-- Menstruasi -->
            <div class="flex-1 flex flex-col items-center gap-2">
              <span class="text-xs font-bold text-primary">4,130</span>
              <div class="w-full relative bg-rose-50 rounded-xl overflow-hidden" style="height:120px">
                <div class="bar-fill absolute bottom-0 w-full bg-gradient-to-t from-primary to-accent-peach rounded-xl" style="height:29%"></div>
              </div>
              <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Menstruasi</span>
            </div>
          </div>

          <div class="mt-5 pt-5 border-t border-rose-50 flex items-center justify-between">
            <p class="text-xs text-slate-400">Total sampel bulan ini</p>
            <p class="text-sm font-bold text-slate-800">38,900 <span class="text-slate-400 font-normal">prediksi</span></p>
          </div>
        </div>

        <!-- Donut chart: Akurasi breakdown + insight -->
        <div class="fade-in delay-3 bg-white rounded-2xl border border-rose-100 shadow-sm p-7 flex flex-col">
          <div class="mb-5">
            <h3 class="text-base font-bold text-slate-800">Tingkat Akurasi</h3>
            <p class="text-xs text-slate-400 mt-0.5">Persentase prediksi tepat sasaran</p>
          </div>

          <!-- Donut SVG -->
          <div class="flex flex-col items-center flex-1 justify-center">
            <div class="relative w-36 h-36">
              <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                <!-- Track -->
                <circle cx="50" cy="50" r="38" fill="none" stroke="#FFF0F1" stroke-width="10"/>
                <!-- Akurat -->
                <circle cx="50" cy="50" r="38" fill="none" stroke="#E35D6A" stroke-width="10"
                  stroke-dasharray="238.76" stroke-dashoffset="19.1"
                  stroke-linecap="round" class="donut-ring"/>
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-2xl font-bold text-slate-800">98.2%</span>
                <span class="text-[10px] text-slate-400 font-semibold">Akurat</span>
              </div>
            </div>
          </div>

          <!-- Insight cards -->
          <div class="mt-5 space-y-2.5">
            <div class="flex items-center justify-between bg-rose-50/60 rounded-xl px-4 py-2.5">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                <span class="text-xs font-semibold text-slate-600">Prediksi Tepat</span>
              </div>
              <span class="text-xs font-bold text-slate-800">38,203</span>
            </div>
            <div class="flex items-center justify-between bg-slate-50 rounded-xl px-4 py-2.5">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                <span class="text-xs font-semibold text-slate-600">Perlu Koreksi</span>
              </div>
              <span class="text-xs font-bold text-slate-800">697</span>
            </div>
            <div class="flex items-center justify-between bg-emerald-50/60 rounded-xl px-4 py-2.5">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span class="text-xs font-semibold text-slate-600">Meningkat MoM</span>
              </div>
              <span class="text-xs font-bold text-emerald-600">+0.4%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ── SECTION 3: TABLE ── -->
      <div class="fade-in delay-5 bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
        <!-- Table header -->
        <div class="p-6 border-b border-rose-50 flex items-center justify-between flex-wrap gap-4">
          <div>
            <h3 class="text-base font-bold text-slate-800">Data Prediksi Per Pengguna</h3>
            <p class="text-xs text-slate-400 mt-0.5">Pantau status siklus dan prediksi kesuburan individu</p>
          </div>
          <div class="flex items-center gap-3">
            <!-- Filter fase -->
            <select id="phaseFilter" onchange="filterPhase()" class="text-xs font-semibold text-slate-600 px-4 py-2 border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-primary/20 outline-none cursor-pointer">
              <option value="">Semua Fase</option>
              <option value="Menstruasi">Menstruasi</option>
              <option value="Folikel">Folikel</option>
              <option value="Ovulasi">Ovulasi</option>
              <option value="Luteal">Luteal</option>
            </select>
            <button onclick="exportTableCSV()" class="flex items-center gap-2 text-slate-600 hover:text-primary px-4 py-2 border border-slate-200 rounded-xl hover:bg-rose-50 transition-all text-xs font-semibold">
              <span class="material-symbols-outlined text-base">download</span>
              Ekspor
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-rose-50/30 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-rose-50">
              <tr>
                <th class="px-6 py-4">Pengguna</th>
                <th class="px-6 py-4 text-center">Usia</th>
                <th class="px-6 py-4 text-center">Panjang Siklus</th>
                <th class="px-6 py-4">Fase Saat Ini</th>
                <th class="px-6 py-4">Estimasi Ovulasi</th>
                <th class="px-6 py-4">Prediksi Menstruasi</th>
                <th class="px-6 py-4 text-center">Akurasi</th>
                <th class="px-6 py-4 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody id="prediksiTableBody" class="divide-y divide-rose-50/70">
            </tbody>
          </table>
        </div>

        <!-- Table footer -->
        <div class="px-6 py-4 border-t border-rose-50 bg-rose-50/20 flex items-center justify-between">
          <p class="text-xs text-slate-400 font-medium" id="tableInfo"></p>
          <div class="flex items-center gap-2" id="tablePagination"></div>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
// ─────────────────────────────────────────
// DATA
// ─────────────────────────────────────────
const prediksiData = [
  { id:"MIR-001", nama:"Aulia Rahmawati", initials:"AR", colorClass:"bg-rose-100 text-primary",    usia:24, siklus:28, fase:"Ovulasi",    ovulasi:"14 Okt 2023", menstruasi:"28 Okt 2023", akurasi:99.1,
    avatar:"https://lh3.googleusercontent.com/aida-public/AB6AXuAqv9phHUAY3dIOEQrFl0IDM5DEX7pHYlygO8eVE2N6D251HOYSO2dWTgMFAkwToWw3DLLfTVPXdDx3-zS9a4T0CyiUXfVMhBL_T-wgtlTHWcXeHUAlax6VopPyJsuXzGP8JZ7oyh2ywvR1q23GSZtAi9E_74v4wiBy5FUxVHAsJUyoAVyc7YthstVjGof_fY-pFafj2zmM6PLNE26cRK6qV2PKyhV6lxYDS1Rt4zXS_M2YV5hoRhVGXbjF25gpz4X2OeUXoccRoG17" },
  { id:"MIR-005", nama:"Siska Putri",     initials:"SP", colorClass:"bg-amber-100 text-amber-600", usia:29, siklus:30, fase:"Folikel",    ovulasi:"18 Okt 2023", menstruasi:"02 Nov 2023", akurasi:97.4,
    avatar:"https://lh3.googleusercontent.com/aida-public/AB6AXuCZ3Lt6tXU_2M5EbB4VReU4qvP7ZNsH6MbXE-xbioQDcm5CTS6legyssW8GmJJ7yVajcQxvSguAZ-W_9SI3VRZlpbzC0AM1xaIPSPFNK9uaCCmxaxKa-H_MsgUvRSdTUSJUiBI5N2tngr6_mAbW7atcVMbBeVzaDs6kPsVjWx4MWIhlzcuO_8yIxzlb0LqJaXQE_u51fVGxy9N_8v9GYbn2MlA9w6DD3T_0DHBHRLplIfRrtvRv1gLYXHom6nYusWU7NXk9q5mjBdx_" },
  { id:"MIR-012", nama:"Dian Kusuma",     initials:"DK", colorClass:"bg-rose-100 text-primary",    usia:31, siklus:26, fase:"Luteal",     ovulasi:"10 Okt 2023", menstruasi:"24 Okt 2023", akurasi:98.8,
    avatar:"https://lh3.googleusercontent.com/aida-public/AB6AXuBrxX3ngnlTnPON-CQhags-3W0ffC-4wjp7-aCTDxsThyrz7sFmMxdP8_nlOS26t4vO3rvGuzaUzWz7Ex671gsWjAv0F-HY1m5hWyX6l4gzWV5C5Dycrwrpxboph1OIrpfoDYI3SSCd_uKzGNc3mTzS0_mFCqvAr_CMpVijIuSfaF_a3OelSD_oeVme3JLLwutQiaBxbK19FdJ5TrOHqvXB3J9wU6Igwe9K7Rz0qd1loSOTipaQGhZM-rCB061G_N8krz0kpzJhpowc" },
  { id:"MIR-028", nama:"Maya Indah",      initials:"MI", colorClass:"bg-slate-100 text-slate-500", usia:22, siklus:29, fase:"Menstruasi", ovulasi:"20 Okt 2023", menstruasi:"07 Nov 2023", akurasi:96.0,
    avatar:"https://lh3.googleusercontent.com/aida-public/AB6AXuC2xSi1fo2uMishxQKCS6zfiGtD1Wv9xylxsH3QjvXAvtXrNNA4N8e7c1z47YlXxCjnpwiUyZI5W_UPeDbhqJquCCWfqpc44X_sWJT98ce0dRVDXZHXiyj1KNSdtJYnar9LX-2EsxtpVoSYC89MnP1hf5b0IEhdyLiJCqSPIJM7IkXf9JsM1WNqdt966YHriSp-Zf32C6Qs5XXZZeIrNZWrHfRZWbiFfADna2gUI2hdqTS8Eurktvr-gTejiBspzxkpAXrC-RkQKjBE" },
  { id:"MIR-045", nama:"Rina Sari",       initials:"RS", colorClass:"bg-rose-100 text-primary",    usia:26, siklus:28, fase:"Ovulasi",    ovulasi:"13 Okt 2023", menstruasi:"27 Okt 2023", akurasi:99.3,
    avatar:"https://lh3.googleusercontent.com/aida-public/AB6AXuDswUKqNZF66oFXmINsmDk-zvQisWXdPdPIDOm9z--d9hY31Ap9CeQJL620xfos2F2EOnSwr-GW01C7xjLlr59zr1BQnIbXvQynXalQdzJpnXyhSLvCpmQKWRNcb7Ahzll4gvtG_HctXMOubPOyoaTSXwlnPv0TMDJhpzIk0q1W-lHv9kCBgYx6MSyxcsjNKbbsQVzB8MYfuWBlb11At9PYoOFumy-8qzM9jCVvtqzpbPUiF6__j7GutW7XCca3o7MfyDl5PAPYo8_j" },
  { id:"MIR-051", nama:"Fitri Handayani", initials:"FH", colorClass:"bg-rose-100 text-primary",    usia:27, siklus:31, fase:"Folikel",    ovulasi:"22 Okt 2023", menstruasi:"05 Nov 2023", akurasi:98.1, avatar:null },
  { id:"MIR-063", nama:"Larasati Dewi",   initials:"LD", colorClass:"bg-amber-100 text-amber-600", usia:23, siklus:27, fase:"Luteal",     ovulasi:"09 Okt 2023", menstruasi:"22 Okt 2023", akurasi:97.7, avatar:null },
  { id:"MIR-071", nama:"Nadia Paramita",  initials:"NP", colorClass:"bg-blue-100 text-blue-500",   usia:30, siklus:28, fase:"Folikel",    ovulasi:"16 Okt 2023", menstruasi:"30 Okt 2023", akurasi:99.0, avatar:null },
  { id:"MIR-082", nama:"Citra Melati",    initials:"CM", colorClass:"bg-rose-100 text-primary",    usia:25, siklus:25, fase:"Menstruasi", ovulasi:"18 Okt 2023", menstruasi:"01 Nov 2023", akurasi:95.8, avatar:null },
  { id:"MIR-099", nama:"Dewi Anggraeni",  initials:"DA", colorClass:"bg-emerald-100 text-emerald-600", usia:33, siklus:33, fase:"Ovulasi", ovulasi:"11 Okt 2023", menstruasi:"28 Okt 2023", akurasi:98.5, avatar:null },
];

// ─────────────────────────────────────────
// PHASE BADGE
// ─────────────────────────────────────────
function phaseBadge(fase) {
  const map = {
    "Menstruasi": "phase-menstruasi",
    "Folikel":    "phase-folikel",
    "Ovulasi":    "phase-ovulasi",
    "Luteal":     "phase-luteal",
  };
  const icons = {
    "Menstruasi": "water_drop",
    "Folikel":    "wb_sunny",
    "Ovulasi":    "favorite",
    "Luteal":     "nights_stay",
  };
  return `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${map[fase]||'bg-slate-100 text-slate-500'}">
    <span class="material-symbols-outlined" style="font-size:12px">${icons[fase]||'circle'}</span>
    ${fase}
  </span>`;
}

function accuracyBar(val) {
  const color = val >= 99 ? '#10B981' : val >= 97 ? '#E35D6A' : '#F59E0B';
  return `<div class="flex flex-col items-center gap-1">
    <span class="text-xs font-bold" style="color:${color}">${val}%</span>
    <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
      <div class="h-full rounded-full" style="width:${val}%;background:${color}"></div>
    </div>
  </div>`;
}

// ─────────────────────────────────────────
// TABLE RENDER
// ─────────────────────────────────────────
let filtered = [...prediksiData];
let page = 1;
const perPage = 5;

function renderTable() {
  const tbody = document.getElementById("prediksiTableBody");
  const start = (page - 1) * perPage;
  const slice = filtered.slice(start, start + perPage);

  if (!slice.length) {
    tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-12 text-center text-slate-400 text-sm">
      <span class="material-symbols-outlined block text-4xl text-slate-300 mb-2">search_off</span>
      Tidak ada data ditemukan
    </td></tr>`;
    document.getElementById("tableInfo").textContent = "";
    document.getElementById("tablePagination").innerHTML = "";
    return;
  }

  tbody.innerHTML = slice.map(u => `
    <tr class="hover:bg-rose-50/20 transition-colors">
      <td class="px-6 py-3.5">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-full ${u.colorClass} flex items-center justify-center text-xs font-bold flex-shrink-0 overflow-hidden">
            ${u.avatar ? `<img src="${u.avatar}" class="w-full h-full object-cover"/>` : u.initials}
          </div>
          <div>
            <p class="text-sm font-bold text-slate-800">${u.nama}</p>
            <p class="text-[10px] text-slate-400">${u.id}</p>
          </div>
        </div>
      </td>
      <td class="px-6 py-3.5 text-center">
        <span class="text-sm font-semibold text-slate-600">${u.usia} thn</span>
      </td>
      <td class="px-6 py-3.5 text-center">
        <span class="text-sm font-semibold text-slate-600">${u.siklus} hari</span>
      </td>
      <td class="px-6 py-3.5">${phaseBadge(u.fase)}</td>
      <td class="px-6 py-3.5">
        <div class="flex items-center gap-1.5">
          <span class="material-symbols-outlined text-emerald-400" style="font-size:14px">favorite</span>
          <span class="text-sm text-slate-600">${u.ovulasi}</span>
        </div>
      </td>
      <td class="px-6 py-3.5">
        <div class="flex items-center gap-1.5">
          <span class="material-symbols-outlined text-primary" style="font-size:14px">water_drop</span>
          <span class="text-sm text-slate-600">${u.menstruasi}</span>
        </div>
      </td>
      <td class="px-6 py-3.5 text-center">${accuracyBar(u.akurasi)}</td>
      <td class="px-6 py-3.5">
        <div class="flex items-center justify-center gap-1">
          <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="Detail">
            <span class="material-symbols-outlined text-lg">visibility</span>
          </button>
          <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-blue-50 hover:text-blue-500 transition-colors" title="Edit">
            <span class="material-symbols-outlined text-lg">edit</span>
          </button>
        </div>
      </td>
    </tr>
  `).join("");

  // Info
  const total = filtered.length;
  const end = Math.min(page * perPage, total);
  document.getElementById("tableInfo").textContent = `Menampilkan ${start+1}–${end} dari ${total} data`;

  // Pagination
  const totalPages = Math.ceil(total / perPage);
  const pag = document.getElementById("tablePagination");
  let html = `<button onclick="goPage(${page-1})" ${page===1?'disabled':''} class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 text-slate-400 hover:bg-rose-50 hover:text-primary transition-all disabled:opacity-30 disabled:cursor-not-allowed">
    <span class="material-symbols-outlined">chevron_left</span></button>`;
  for (let i=1; i<=totalPages; i++) {
    html += `<button onclick="goPage(${i})" class="w-9 h-9 flex items-center justify-center rounded-xl font-bold text-sm transition-all ${i===page ? 'bg-primary text-white shadow-md shadow-primary/20' : 'border border-rose-100 text-slate-600 hover:bg-rose-50'}">${i}</button>`;
  }
  html += `<button onclick="goPage(${page+1})" ${page>=totalPages?'disabled':''} class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 text-slate-400 hover:bg-rose-50 hover:text-primary transition-all disabled:opacity-30 disabled:cursor-not-allowed">
    <span class="material-symbols-outlined">chevron_right</span></button>`;
  pag.innerHTML = html;
}

function goPage(p) {
  const max = Math.ceil(filtered.length / perPage);
  if (p < 1 || p > max) return;
  page = p;
  renderTable();
}

function filterPhase() {
  const val = document.getElementById("phaseFilter").value;
  filtered = val ? prediksiData.filter(u => u.fase === val) : [...prediksiData];
  page = 1;
  renderTable();
}

function exportTableCSV() {
  const headers = ["ID","Nama","Usia","Siklus (hari)","Fase","Est. Ovulasi","Prediksi Menstruasi","Akurasi (%)"];
  const rows = filtered.map(u => [u.id,u.nama,u.usia,u.siklus,u.fase,u.ovulasi,u.menstruasi,u.akurasi]);
  const csv = [headers,...rows].map(r=>r.join(",")).join("\n");
  const a = document.createElement("a");
  a.href = URL.createObjectURL(new Blob([csv],{type:"text/csv"}));
  a.download = "mirai_prediksi.csv";
  a.click();
}

renderTable();
</script>
</body>
</html>
