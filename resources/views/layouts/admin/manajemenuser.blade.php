<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>MIRAI Admin Dashboard - Manajemen Data Pengguna</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
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
<style type="text/tailwindcss">
  @layer base {
    body { font-family: 'Inter', sans-serif; }
  }
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
</style>

<style>
  /* Filter dropdown */
  #filterDropdown { display: none; }
  #filterDropdown.open { display: block; }

  /* Modal */
  #filterModal { display: none; }
  #filterModal.open { display: flex; }
</style>
</head>

<body class="bg-white text-slate-800">
<div class="flex h-screen overflow-hidden">

  <!-- SIDEBAR (dari kode 1) -->
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
      <a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl transition-all shadow-sm" href="#">
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

  <!-- MAIN AREA -->
  <div class="flex-1 flex flex-col min-w-0">

    <!-- HEADER (dari kode 1) -->
    <header class="h-20 bg-white border-b border-rose-50 flex items-center justify-between px-10 flex-shrink-0">
      <div class="flex items-center gap-4">
        <h2 class="text-xl font-semibold text-slate-800">Manajemen Data Pengguna</h2>
      </div>
      <div class="flex items-center gap-6">
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
          <input
            id="searchInput"
            class="pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 w-64"
            placeholder="Cari pengguna..."
            type="text"
            oninput="filterTable()"
          />
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
            <img alt="Admin Profile" class="w-full h-full object-cover"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuBd5O7bCkEck3mAE5X76r1tzFmTi7vKs91dKvImYSOPIZA6ffLRGC-STIkifTXUM4dKJ9Nvbv-zKUBcIhJS77ZU5eq6mfSxCy9V3P37-VgdAB4HStRt-tHkMDuxTXsSe8QJZPEJk9yiTrOOa_RBPQ97S-m-R8PDN0JC77F2SEUSSf8FGtzZsGatzd963VH6Zt8vP_uYyN737GksaTEQtNYrn0XDnRS_nOBBStzyRCHfmx8K9P9UxcAPPj9aSLEa-G6uVC8w_qVHKdzY"/>
          </div>
        </div>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 overflow-y-auto bg-slate-50/50 p-10">
      <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">

        <!-- Table Header -->
        <div class="p-8 flex items-center justify-between border-b border-rose-50">
          <div>
            <h3 class="text-xl font-bold text-slate-800">Daftar Pengguna Aktif</h3>
            <p class="text-slate-400 text-sm mt-1">Kelola informasi profil dan status akun pengguna MIRAI.</p>
          </div>
          <div class="flex items-center gap-3">
            <!-- Filter Button -->
            <div class="relative">
              <button
                onclick="toggleFilterModal()"
                class="flex items-center gap-2 text-slate-600 hover:text-primary px-4 py-2.5 border border-slate-200 rounded-xl hover:bg-rose-50 transition-all text-sm font-semibold"
              >
                <span class="material-symbols-outlined text-lg">filter_list</span>
                <span>Filter</span>
              </button>
            </div>

            <!-- Export Button -->
            <button
              onclick="exportCSV()"
              class="flex items-center gap-2 text-slate-600 hover:text-primary px-4 py-2.5 border border-slate-200 rounded-xl hover:bg-rose-50 transition-all text-sm font-semibold"
            >
              <span class="material-symbols-outlined text-lg">download</span>
              <span>Ekspor</span>
            </button>

            <!-- Tambah User Button -->
            <button class="flex items-center gap-2 bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-primary/90 transition-all shadow-md shadow-primary/20">
              <span class="material-symbols-outlined text-lg">person_add</span>
              Tambah Pengguna Baru
            </button>
          </div>
        </div>

        <!-- Active Filters Display -->
        <div id="activeFilters" class="hidden px-8 py-3 bg-rose-50/40 border-b border-rose-50 flex items-center gap-2 flex-wrap">
          <span class="text-xs font-semibold text-slate-500">Filter aktif:</span>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
          <table class="w-full text-left" id="userTable">
            <thead class="bg-rose-50/30 text-[11px] uppercase tracking-widest text-slate-500 font-bold border-b border-rose-50">
              <tr>
                <th class="px-8 py-5">Avatar</th>
                <th class="px-6 py-5">Nama Lengkap</th>
                <th class="px-6 py-5">Email</th>
                <th class="px-6 py-5">No. Telepon</th>
                <th class="px-6 py-5 text-center">Usia</th>
                <th class="px-6 py-5 text-center">Berat Badan</th>
                <th class="px-6 py-5 text-center">Tinggi Badan</th>
                <th class="px-6 py-5">Lokasi</th>
                <th class="px-6 py-5">Status</th>
                <th class="px-8 py-5 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-rose-50" id="tableBody">
              <!-- Rows populated by JS -->
            </tbody>
          </table>
        </div>

        <!-- Summary Stats -->
        <div class="px-8 py-4 border-t border-rose-50 bg-rose-50/20 flex items-center gap-6 flex-wrap">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
              <span class="material-symbols-outlined text-slate-500" style="font-size:18px">group</span>
            </div>
            <div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Pengguna</p>
              <p class="text-sm font-bold text-slate-800" id="statTotal">0</p>
            </div>
          </div>
          <div class="w-[1px] h-8 bg-rose-100"></div>
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
              <span class="material-symbols-outlined text-emerald-500" style="font-size:18px">check_circle</span>
            </div>
            <div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Aktif</p>
              <p class="text-sm font-bold text-emerald-600" id="statAktif">0</p>
            </div>
          </div>
          <div class="w-[1px] h-8 bg-rose-100"></div>
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
              <span class="material-symbols-outlined text-amber-500" style="font-size:18px">schedule</span>
            </div>
            <div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Menunggu</p>
              <p class="text-sm font-bold text-amber-600" id="statMenunggu">0</p>
            </div>
          </div>
          <div class="w-[1px] h-8 bg-rose-100"></div>
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center">
              <span class="material-symbols-outlined text-primary" style="font-size:18px">block</span>
            </div>
            <div>
              <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Nonaktif</p>
              <p class="text-sm font-bold text-primary" id="statNonaktif">0</p>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="p-8 border-t border-rose-50 flex items-center justify-between">
          <p class="text-xs font-bold text-slate-400 uppercase tracking-widest" id="paginationInfo"></p>
          <div class="flex items-center gap-2" id="paginationControls"></div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- FILTER MODAL -->
<div id="filterModal" class="fixed inset-0 z-50 items-center justify-center bg-black/30 backdrop-blur-sm">
  <div class="bg-white rounded-2xl shadow-xl border border-rose-100 w-[440px] p-8 mx-4">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-bold text-slate-800">Filter Pengguna</h3>
      <button onclick="toggleFilterModal()" class="p-1.5 hover:bg-rose-50 rounded-lg transition-colors text-slate-400 hover:text-primary">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <div class="space-y-5">
      <!-- Filter Status -->
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Status</label>
        <div class="flex gap-2 flex-wrap">
          <button onclick="toggleFilterChip(this, 'status', 'Aktif')"
            class="filter-chip px-4 py-2 rounded-full text-xs font-semibold border border-slate-200 text-slate-600 hover:border-primary hover:text-primary transition-all">
            Aktif
          </button>
          <button onclick="toggleFilterChip(this, 'status', 'Menunggu')"
            class="filter-chip px-4 py-2 rounded-full text-xs font-semibold border border-slate-200 text-slate-600 hover:border-primary hover:text-primary transition-all">
            Menunggu
          </button>
          <button onclick="toggleFilterChip(this, 'status', 'Dinonaktifkan')"
            class="filter-chip px-4 py-2 rounded-full text-xs font-semibold border border-slate-200 text-slate-600 hover:border-primary hover:text-primary transition-all">
            Dinonaktifkan
          </button>
        </div>
      </div>

      <!-- Filter Usia -->
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Rentang Usia</label>
        <div class="flex gap-3 items-center">
          <input id="ageMin" type="number" placeholder="Min" min="0" max="100"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"/>
          <span class="text-slate-400 font-medium">–</span>
          <input id="ageMax" type="number" placeholder="Max" min="0" max="100"
            class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"/>
        </div>
      </div>

      <!-- Urutkan -->
      <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Urutkan berdasarkan</label>
        <select id="sortBy" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
          <option value="">-- Pilih --</option>
          <option value="nama">Nama (A-Z)</option>
          <option value="nama-desc">Nama (Z-A)</option>
          <option value="usia">Usia (Termuda)</option>
          <option value="usia-desc">Usia (Tertua)</option>
          <option value="berat">Berat Badan (Ringan)</option>
          <option value="berat-desc">Berat Badan (Berat)</option>
        </select>
      </div>
    </div>

    <div class="flex gap-3 mt-8">
      <button onclick="resetFilters()" class="flex-1 px-4 py-3 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
        Reset
      </button>
      <button onclick="applyFilters()" class="flex-1 px-4 py-3 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all shadow-md shadow-primary/20">
        Terapkan Filter
      </button>
    </div>
  </div>
</div>

<script>
  // =====================
  // DATA (dari kode 1, diperluas)
  // =====================
  const users = [
    {
      id: "MIR-2024-001",
      nama: "Aulia Rahmawati",
      email: "aulia.rahma@gmail.com",
      telepon: "+62 812-3456-7890",
      usia: 24,
      berat: 55,
      tinggi: 160,
      status: "Aktif",
      lokasi: "DKI Jakarta",
      initials: "AR",
      avatarColor: "bg-rose-100 text-primary",
      avatar: "https://lh3.googleusercontent.com/aida-public/AB6AXuAqv9phHUAY3dIOEQrFl0IDM5DEX7pHYlygO8eVE2N6D251HOYSO2dWTgMFAkwToWw3DLLfTVPXdDx3-zS9a4T0CyiUXfVMhBL_T-wgtlTHWcXeHUAlax6VopPyJsuXzGP8JZ7oyh2ywvR1q23GSZtAi9E_74v4wiBy5FUxVHAsJUyoAVyc7YthstVjGof_fY-pFafj2zmM6PLNE26cRK6qV2PKyhV6lxYDS1Rt4zXS_M2YV5hoRhVGXbjF25gpz4X2OeUXoccRoG17"
    },
    {
      id: "MIR-2024-005",
      nama: "Siska Putri",
      email: "siska.putri99@yahoo.com",
      telepon: "+62 856-9122-3344",
      usia: 28,
      berat: 62,
      tinggi: 165,
      status: "Menunggu",
      lokasi: "Jawa Barat",
      initials: "SP",
      avatarColor: "bg-amber-100 text-amber-600",
      avatar: "https://lh3.googleusercontent.com/aida-public/AB6AXuCZ3Lt6tXU_2M5EbB4VReU4qvP7ZNsH6MbXE-xbioQDcm5CTS6legyssW8GmJJ7yVajcQxvSguAZ-W_9SI3VRZlpbzC0AM1xaIPSPFNK9uaCCmxaxKa-H_MsgUvRSdTUSJUiBI5N2tngr6_mAbW7atcVMbBeVzaDs6kPsVjWx4MWIhlzcuO_8yIxzlb0LqJaXQE_u51fVGxy9N_8v9GYbn2MlA9w6DD3T_0DHBHRLplIfRrtvRv1gLYXHom6nYusWU7NXk9q5mjBdx_"
    },
    {
      id: "MIR-2024-012",
      nama: "Dian Kusuma",
      email: "dian_ksm@outlook.com",
      telepon: "+62 821-4455-6677",
      usia: 31,
      berat: 58,
      tinggi: 158,
      status: "Aktif",
      lokasi: "Jawa Tengah",
      initials: "DK",
      avatarColor: "bg-rose-100 text-primary",
      avatar: "https://lh3.googleusercontent.com/aida-public/AB6AXuBrxX3ngnlTnPON-CQhags-3W0ffC-4wjp7-aCTDxsThyrz7sFmMxdP8_nlOS26t4vO3rvGuzaUzWz7Ex671gsWjAv0F-HY1m5hWyX6l4gzWV5C5Dycrwrpxboph1OIrpfoDYI3SSCd_uKzGNc3mTzS0_mFCqvAr_CMpVijIuSfaF_a3OelSD_oeVme3JLLwutQiaBxbK19FdJ5TrOHqvXB3J9wU6Igwe9K7Rz0qd1loSOTipaQGhZM-rCB061G_N8krz0kpzJhpowc"
    },
    {
      id: "MIR-2024-028",
      nama: "Maya Indah",
      email: "maya.indah@gmail.com",
      telepon: "+62 878-1122-3399",
      usia: 22,
      berat: 48,
      tinggi: 155,
      status: "Dinonaktifkan",
      lokasi: "Jawa Timur",
      initials: "MI",
      avatarColor: "bg-slate-100 text-slate-500",
      avatar: "https://lh3.googleusercontent.com/aida-public/AB6AXuC2xSi1fo2uMishxQKCS6zfiGtD1Wv9xylxsH3QjvXAvtXrNNA4N8e7c1z47YlXxCjnpwiUyZI5W_UPeDbhqJquCCWfqpc44X_sWJT98ce0dRVDXZHXiyj1KNSdtJYnar9LX-2EsxtpVoSYC89MnP1hf5b0IEhdyLiJCqSPIJM7IkXf9JsM1WNqdt966YHriSp-Zf32C6Qs5XXZZeIrNZWrHfRZWbiFfADna2gUI2hdqTS8Eurktvr-gTejiBspzxkpAXrC-RkQKjBE"
    },
    {
      id: "MIR-2024-045",
      nama: "Rina Sari",
      email: "rina.sari@yahoo.id",
      telepon: "+62 899-7788-0022",
      usia: 26,
      berat: 53,
      tinggi: 162,
      status: "Aktif",
      lokasi: "Banten",
      initials: "RS",
      avatarColor: "bg-rose-100 text-primary",
      avatar: "https://lh3.googleusercontent.com/aida-public/AB6AXuDswUKqNZF66oFXmINsmDk-zvQisWXdPdPIDOm9z--d9hY31Ap9CeQJL620xfos2F2EOnSwr-GW01C7xjLlr59zr1BQnIbXvQynXalQdzJpnXyhSLvCpmQKWRNcb7Ahzll4gvtG_HctXMOubPOyoaTSXwlnPv0TMDJhpzIk0q1W-lHv9kCBgYx6MSyxcsjNKbbsQVzB8MYfuWBlb11At9PYoOFumy-8qzM9jCVvtqzpbPUiF6__j7GutW7XCca3o7MfyDl5PAPYo8_j"
    },
    {
      id: "MIR-2024-051",
      nama: "Fitri Handayani",
      email: "fitri.h@gmail.com",
      telepon: "+62 813-5566-7788",
      usia: 27,
      berat: 60,
      tinggi: 163,
      status: "Aktif",
      lokasi: "Sumatera Utara",
      initials: "FH",
      avatarColor: "bg-rose-100 text-primary",
      avatar: null
    },
    {
      id: "MIR-2024-063",
      nama: "Larasati Dewi",
      email: "laras.dewi@email.com",
      telepon: "+62 857-2233-4455",
      usia: 23,
      berat: 50,
      tinggi: 157,
      status: "Menunggu",
      lokasi: "DKI Jakarta",
      initials: "LD",
      avatarColor: "bg-amber-100 text-amber-600",
      avatar: null
    }
  ];

  // =====================
  // STATE
  // =====================
  let filteredUsers = [...users];
  let currentPage = 1;
  const rowsPerPage = 5;
  let activeFilters = { status: [], ageMin: null, ageMax: null, sortBy: "" };

  // =====================
  // STATUS BADGE
  // =====================
  function getStatusBadge(status) {
    const map = {
      "Aktif": "bg-emerald-50 text-emerald-600 border border-emerald-100",
      "Menunggu": "bg-amber-50 text-amber-600 border border-amber-100",
      "Dinonaktifkan": "bg-rose-50 text-primary border border-rose-100"
    };
    return `<span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full ${map[status] || 'bg-slate-50 text-slate-500'}">${status}</span>`;
  }

  // =====================
  // RENDER TABLE
  // =====================
  function renderTable() {
    const tbody = document.getElementById("tableBody");
    const start = (currentPage - 1) * rowsPerPage;
    const pageData = filteredUsers.slice(start, start + rowsPerPage);

    if (pageData.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="10" class="px-8 py-16 text-center text-slate-400">
            <div class="flex flex-col items-center gap-2">
              <span class="material-symbols-outlined text-4xl text-slate-300">search_off</span>
              <p class="font-medium">Tidak ada data yang cocok</p>
            </div>
          </td>
        </tr>`;
      updatePagination();
      return;
    }

    tbody.innerHTML = pageData.map(u => `
      <tr class="hover:bg-rose-50/10 transition-colors">
        <td class="px-8 py-4">
          <div class="w-10 h-10 rounded-full ${u.avatarColor} flex items-center justify-center font-bold text-xs overflow-hidden border border-rose-100">
            ${u.avatar
              ? `<img src="${u.avatar}" alt="${u.nama}" class="w-full h-full object-cover"/>`
              : u.initials
            }
          </div>
        </td>
        <td class="px-6 py-4">
          <p class="font-bold text-slate-800">${u.nama}</p>
          <p class="text-[10px] text-slate-400 font-medium">ID: ${u.id}</p>
        </td>
        <td class="px-6 py-4 text-slate-600 text-sm">${u.email}</td>
        <td class="px-6 py-4 text-slate-600 text-sm">${u.telepon}</td>
        <td class="px-6 py-4 text-center text-slate-600 text-sm font-semibold">${u.usia} thn</td>
        <td class="px-6 py-4 text-center text-slate-600 text-sm">${u.berat} kg</td>
        <td class="px-6 py-4 text-center text-slate-600 text-sm">${u.tinggi} cm</td>
        <td class="px-6 py-4">
          <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-slate-400" style="font-size:14px">location_on</span>
            <span class="text-sm text-slate-600">${u.lokasi}</span>
          </div>
        </td>
        <td class="px-6 py-4">${getStatusBadge(u.status)}</td>
        <td class="px-8 py-4">
          <div class="flex items-center justify-center gap-1">
            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="Detail">
              <span class="material-symbols-outlined text-lg">visibility</span>
            </button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Edit">
              <span class="material-symbols-outlined text-lg">edit</span>
            </button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-primary transition-colors" title="Hapus">
              <span class="material-symbols-outlined text-lg">delete</span>
            </button>
          </div>
        </td>
      </tr>
    `).join("");

    updatePagination();
    updateStats();
  }

  // =====================
  // STATS
  // =====================
  function updateStats() {
    const total = users.length;
    const aktif = users.filter(u => u.status === "Aktif").length;
    const menunggu = users.filter(u => u.status === "Menunggu").length;
    const nonaktif = users.filter(u => u.status === "Dinonaktifkan").length;
    document.getElementById("statTotal").textContent = total.toLocaleString('id-ID');
    document.getElementById("statAktif").textContent = aktif.toLocaleString('id-ID');
    document.getElementById("statMenunggu").textContent = menunggu.toLocaleString('id-ID');
    document.getElementById("statNonaktif").textContent = nonaktif.toLocaleString('id-ID');
  }

  // =====================
  // PAGINATION
  // =====================
  function updatePagination() {
    const total = filteredUsers.length;
    const totalPages = Math.ceil(total / rowsPerPage);
    const start = (currentPage - 1) * rowsPerPage + 1;
    const end = Math.min(currentPage * rowsPerPage, total);

    document.getElementById("paginationInfo").textContent =
      total > 0
        ? `Menampilkan ${start}-${end} dari ${total.toLocaleString('id-ID')} pengguna`
        : "Tidak ada pengguna ditemukan";

    const controls = document.getElementById("paginationControls");
    const pages = [];

    // Prev
    pages.push(`
      <button onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}
        class="w-10 h-10 flex items-center justify-center rounded-xl border border-rose-100 text-slate-400 hover:bg-rose-50 hover:text-primary transition-all disabled:opacity-30 disabled:cursor-not-allowed">
        <span class="material-symbols-outlined">chevron_left</span>
      </button>`);

    // Pages
    for (let i = 1; i <= Math.min(totalPages, 5); i++) {
      pages.push(`
        <button onclick="goToPage(${i})"
          class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all ${
            i === currentPage
              ? 'bg-primary text-white shadow-md shadow-primary/20'
              : 'border border-rose-100 text-slate-600 hover:bg-rose-50'
          }">
          ${i}
        </button>`);
    }

    // Next
    pages.push(`
      <button onclick="goToPage(${currentPage + 1})" ${currentPage >= totalPages ? 'disabled' : ''}
        class="w-10 h-10 flex items-center justify-center rounded-xl border border-rose-100 text-slate-400 hover:bg-rose-50 hover:text-primary transition-all disabled:opacity-30 disabled:cursor-not-allowed">
        <span class="material-symbols-outlined">chevron_right</span>
      </button>`);

    controls.innerHTML = pages.join("");
  }

  function goToPage(page) {
    const totalPages = Math.ceil(filteredUsers.length / rowsPerPage);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderTable();
  }

  // =====================
  // SEARCH
  // =====================
  function filterTable() {
    const query = document.getElementById("searchInput").value.toLowerCase();
    applyAllFilters(query);
  }

  // =====================
  // FILTER MODAL
  // =====================
  function toggleFilterModal() {
    const modal = document.getElementById("filterModal");
    modal.classList.toggle("open");
  }

  let selectedStatusFilters = [];

  function toggleFilterChip(btn, type, value) {
    if (type === 'status') {
      const idx = selectedStatusFilters.indexOf(value);
      if (idx > -1) {
        selectedStatusFilters.splice(idx, 1);
        btn.classList.remove("bg-primary", "text-white", "border-primary");
        btn.classList.add("border-slate-200", "text-slate-600");
      } else {
        selectedStatusFilters.push(value);
        btn.classList.add("bg-primary", "text-white", "border-primary");
        btn.classList.remove("border-slate-200", "text-slate-600");
      }
    }
  }

  function applyFilters() {
    activeFilters.status = [...selectedStatusFilters];
    activeFilters.ageMin = parseInt(document.getElementById("ageMin").value) || null;
    activeFilters.ageMax = parseInt(document.getElementById("ageMax").value) || null;
    activeFilters.sortBy = document.getElementById("sortBy").value;

    toggleFilterModal();
    currentPage = 1;
    applyAllFilters(document.getElementById("searchInput").value.toLowerCase());
    renderActiveFilterTags();
  }

  function applyAllFilters(searchQuery = "") {
    let result = [...users];

    // Search
    if (searchQuery) {
      result = result.filter(u =>
        u.nama.toLowerCase().includes(searchQuery) ||
        u.email.toLowerCase().includes(searchQuery) ||
        u.id.toLowerCase().includes(searchQuery)
      );
    }

    // Status filter
    if (activeFilters.status.length > 0) {
      result = result.filter(u => activeFilters.status.includes(u.status));
    }

    // Age filter
    if (activeFilters.ageMin !== null) {
      result = result.filter(u => u.usia >= activeFilters.ageMin);
    }
    if (activeFilters.ageMax !== null) {
      result = result.filter(u => u.usia <= activeFilters.ageMax);
    }

    // Sort
    if (activeFilters.sortBy) {
      const sortMap = {
        "nama": (a, b) => a.nama.localeCompare(b.nama),
        "nama-desc": (a, b) => b.nama.localeCompare(a.nama),
        "usia": (a, b) => a.usia - b.usia,
        "usia-desc": (a, b) => b.usia - a.usia,
        "berat": (a, b) => a.berat - b.berat,
        "berat-desc": (a, b) => b.berat - a.berat,
      };
      if (sortMap[activeFilters.sortBy]) {
        result.sort(sortMap[activeFilters.sortBy]);
      }
    }

    filteredUsers = result;
    currentPage = 1;
    renderTable();
  }

  function renderActiveFilterTags() {
    const container = document.getElementById("activeFilters");
    const tags = [];

    if (activeFilters.status.length > 0) {
      activeFilters.status.forEach(s => {
        tags.push(`<span class="flex items-center gap-1 px-3 py-1 bg-primary/10 text-primary text-xs font-semibold rounded-full">
          ${s}
          <button onclick="removeStatusFilter('${s}')" class="hover:text-primary/60">
            <span class="material-symbols-outlined text-xs" style="font-size:14px">close</span>
          </button>
        </span>`);
      });
    }
    if (activeFilters.ageMin || activeFilters.ageMax) {
      const label = `Usia: ${activeFilters.ageMin || '0'} – ${activeFilters.ageMax || '∞'}`;
      tags.push(`<span class="flex items-center gap-1 px-3 py-1 bg-primary/10 text-primary text-xs font-semibold rounded-full">
        ${label}
        <button onclick="removeAgeFilter()" class="hover:text-primary/60">
          <span class="material-symbols-outlined text-xs" style="font-size:14px">close</span>
        </button>
      </span>`);
    }

    if (tags.length > 0) {
      container.innerHTML = `<span class="text-xs font-semibold text-slate-500">Filter aktif:</span>` + tags.join("");
      container.classList.remove("hidden");
      container.classList.add("flex");
    } else {
      container.classList.add("hidden");
      container.classList.remove("flex");
    }
  }

  function removeStatusFilter(val) {
    activeFilters.status = activeFilters.status.filter(s => s !== val);
    selectedStatusFilters = selectedStatusFilters.filter(s => s !== val);
    // Reset chip visuals
    document.querySelectorAll(".filter-chip").forEach(btn => {
      if (btn.textContent.trim() === val) {
        btn.classList.remove("bg-primary", "text-white", "border-primary");
        btn.classList.add("border-slate-200", "text-slate-600");
      }
    });
    currentPage = 1;
    applyAllFilters(document.getElementById("searchInput").value.toLowerCase());
    renderActiveFilterTags();
  }

  function removeAgeFilter() {
    activeFilters.ageMin = null;
    activeFilters.ageMax = null;
    document.getElementById("ageMin").value = "";
    document.getElementById("ageMax").value = "";
    currentPage = 1;
    applyAllFilters(document.getElementById("searchInput").value.toLowerCase());
    renderActiveFilterTags();
  }

  function resetFilters() {
    selectedStatusFilters = [];
    activeFilters = { status: [], ageMin: null, ageMax: null, sortBy: "" };
    document.getElementById("ageMin").value = "";
    document.getElementById("ageMax").value = "";
    document.getElementById("sortBy").value = "";
    document.querySelectorAll(".filter-chip").forEach(btn => {
      btn.classList.remove("bg-primary", "text-white", "border-primary");
      btn.classList.add("border-slate-200", "text-slate-600");
    });
    filteredUsers = [...users];
    currentPage = 1;
    renderTable();
    renderActiveFilterTags();
    toggleFilterModal();
  }

  // =====================
  // EXPORT CSV
  // =====================
  function exportCSV() {
    const headers = ["ID", "Nama", "Email", "Telepon", "Usia", "Berat (kg)", "Tinggi (cm)", "Lokasi", "Status"];
    const rows = filteredUsers.map(u => [u.id, u.nama, u.email, u.telepon, u.usia, u.berat, u.tinggi, u.lokasi, u.status]);
    const csv = [headers, ...rows].map(r => r.join(",")).join("\n");
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "mirai_pengguna.csv";
    a.click();
  }

  // Close modal on backdrop click
  document.getElementById("filterModal").addEventListener("click", function(e) {
    if (e.target === this) toggleFilterModal();
  });

  // =====================
  // INIT
  // =====================
  renderTable();
</script>
</body>
</html>
