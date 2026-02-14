
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MIRAI Admin Dashboard - Sistem Manajemen Prediksi Menstruasi">
    <title>MIRAI Admin - Dashboard</title>

    <!-- ============================================
         DEPENDENCIES
         ============================================ -->

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Google Fonts - STANDARDIZED: Inter Only -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <!-- ============================================
         TAILWIND CONFIGURATION

         CATATAN:
         - Warna primary: #E35D6A (pink/rose utama)
         - Font family: Inter (konsisten semua halaman)
         - Border radius: Disesuaikan untuk look modern
         ============================================ -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#E35D6A",
                        "primary-dark": "#C94854",
                        "accent-peach": "#FFB7A5",
                        "sidebar-pink": "#FFF0F1",
                        "blush": "#FDF2F3",
                    },
                    fontFamily: {
                        sans: ["Inter", "system-ui", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        lg: "0.75rem",
                        xl: "1rem",
                        "2xl": "1.5rem",
                    },
                },
            },
        }
    </script>

    <!-- ============================================
         CUSTOM STYLES
         ============================================ -->
    <style>
        /* ========================================
           BASE STYLES
           ======================================== */
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* Material Icons Configuration */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* ========================================
           ACCESSIBILITY
           ======================================== */

        /* Focus indicator untuk keyboard navigation */
        *:focus-visible {
            outline: 3px solid #E35D6A;
            outline-offset: 2px;
        }

        /* Skip to content link (accessibility) */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: #E35D6A;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 0 0 8px 0;
            z-index: 100;
        }

        .skip-link:focus {
            top: 0;
        }

        /* ========================================
           SIDEBAR - MOBILE RESPONSIVE
           ======================================== */

        .sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mobile: Sidebar tersembunyi default */
        @media (max-width: 1024px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 50;
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }
        }

        /* Active navigation indicator (vertical bar) */
        .nav-link.active {
            position: relative;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: white;
            border-radius: 0 4px 4px 0;
        }

        /* ========================================
           ANIMATIONS
           ======================================== */

        /* Fade in animation untuk loading cards */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            opacity: 0;
            animation: fadeIn 0.4s ease-out forwards;
        }

        /* Staggered delays untuk sequential animation */
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.1s; }
        .delay-3 { animation-delay: 0.15s; }
        .delay-4 { animation-delay: 0.2s; }
        .delay-5 { animation-delay: 0.25s; }
        .delay-6 { animation-delay: 0.3s; }

        /* Chart line drawing animation */
        @keyframes drawLine {
            to {
                stroke-dashoffset: 0;
            }
        }

        .line-animate {
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            animation: drawLine 1.5s ease forwards;
            animation-delay: 0.3s;
        }

        /* ========================================
           MOBILE OVERLAY
           ======================================== */

        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-overlay.active {
            display: block;
            opacity: 1;
        }

        /* ========================================
           SCROLLBAR STYLING
           ======================================== */

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #FFF0F1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #FFB7A5;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #E35D6A;
        }

        /* ========================================
           INTERACTIVE EFFECTS
           ======================================== */

        /* Stat card hover effect */
        .stat-card {
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(227, 93, 106, 0.15);
        }

        /* Table row hover */
        .table-row-hover {
            transition: background-color 0.15s ease;
        }

        .table-row-hover:hover {
            background-color: rgba(255, 240, 241, 0.3);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- ============================================
         ACCESSIBILITY: Skip to Content
         ============================================ -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- ============================================
         MOBILE OVERLAY (untuk backdrop sidebar)
         ============================================ -->
    <div id="mobileOverlay" class="mobile-overlay" onclick="toggleSidebar()"></div>

    <!-- ============================================
         MAIN LAYOUT CONTAINER
         ============================================ -->
    <div class="flex h-screen overflow-hidden">

        <!-- ============================================
             SIDEBAR NAVIGATION

             CATATAN:
             - Width: 288px (18rem)
             - Background: #FFF0F1 (sidebar-pink)
             - Fixed di desktop, slide-in di mobile
             - Active state: background primary
             ============================================ -->
        <aside id="sidebar" class="sidebar w-72 flex-shrink-0 bg-sidebar-pink flex flex-col border-r border-rose-100">

            <!-- Logo & Brand -->
            <div class="p-8 flex items-center gap-3">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-md shadow-primary/20">
                    <span class="material-icons-round text-white text-2xl" aria-hidden="true">auto_awesome</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-primary">MIRAI</h1>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-6 space-y-2 overflow-y-auto custom-scrollbar" aria-label="Main navigation">

                <!-- Dashboard (ACTIVE) -->
                <a href="#dashboard"
                   class="nav-link active flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl transition-all shadow-sm"
                   aria-current="page">
                    <span class="material-symbols-outlined" aria-hidden="true">dashboard</span>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Data Pengguna -->
                <a href="#data-pengguna"
                   class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all">
                    <span class="material-symbols-outlined" aria-hidden="true">person</span>
                    <span class="font-medium">Data Pengguna</span>
                </a>

                <!-- Data Siklus Menstruasi -->
                <a href="#data-siklus"
                   class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all">
                    <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                    <span class="font-medium">Data Siklus Menstruasi</span>
                </a>

                <!-- Prediksi & Kesuburan -->
                <a href="#prediksi"
                   class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all">
                    <span class="material-symbols-outlined" aria-hidden="true">health_and_safety</span>
                    <span class="font-medium">Prediksi & Kesuburan</span>
                </a>

                <!-- Analitik & Grafik -->
                <a href="#analitik"
                   class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all">
                    <span class="material-symbols-outlined" aria-hidden="true">analytics</span>
                    <span class="font-medium">Analitik & Grafik</span>
                </a>

                <!-- Laporan -->
                <a href="#laporan"
                   class="nav-link flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all">
                    <span class="material-symbols-outlined" aria-hidden="true">description</span>
                    <span class="font-medium">Laporan</span>
                </a>

            </nav>

            <!-- Bottom Actions (Settings & Logout) -->
            <div class="p-6 border-t border-rose-100 space-y-2">

                <a href="#pengaturan"
                   class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-rose-100 hover:text-primary rounded-xl transition-all">
                    <span class="material-symbols-outlined" aria-hidden="true">settings</span>
                    <span class="font-medium">Pengaturan</span>
                </a>

                <button onclick="handleLogout()"
                        class="w-full flex items-center gap-3 px-4 py-3 text-rose-500 hover:bg-rose-100 rounded-xl transition-all"
                        aria-label="Keluar dari sistem">
                    <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                    <span class="font-medium">Keluar</span>
                </button>

            </div>

        </aside>

        <!-- ============================================
             MAIN CONTENT AREA
             ============================================ -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- ============================================
                 HEADER BAR

                 KOMPONEN:
                 - Mobile menu button (hidden di desktop)
                 - Page title
                 - Search bar
                 - Notifications
                 - User profile
                 ============================================ -->
            <header class="h-20 bg-white border-b border-rose-50 flex items-center justify-between px-6 lg:px-10 flex-shrink-0">

                <!-- Left: Mobile Menu + Title -->
                <div class="flex items-center gap-4">

                    <!-- Mobile Menu Toggle Button -->
                    <button onclick="toggleSidebar()"
                            class="lg:hidden p-2 text-slate-600 hover:text-primary hover:bg-rose-50 rounded-lg transition-all"
                            aria-label="Toggle menu"
                            aria-expanded="false">
                        <span class="material-symbols-outlined">menu</span>
                    </button>

                    <!-- Page Title -->
                    <h2 class="text-lg lg:text-xl font-semibold text-slate-800">Ringkasan Dashboard</h2>
                </div>

                <!-- Right: Search, Notifications, Profile -->
                <div class="flex items-center gap-3 lg:gap-6">

                    <!-- Search Bar (hidden di mobile) -->
                    <div class="relative hidden md:block">
                        <label for="searchInput" class="sr-only">Cari log sistem</label>
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" aria-hidden="true">
                            search
                        </span>
                        <input id="searchInput"
                               type="search"
                               class="pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 w-48 lg:w-64"
                               placeholder="Cari log sistem..."/>
                    </div>

                    <!-- Notifications Button -->
                    <button class="relative p-2 text-slate-400 hover:text-primary hover:bg-rose-50 rounded-lg transition-all"
                            aria-label="Notifikasi (3 belum dibaca)">
                        <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
                        <!-- Badge: unread count -->
                        <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full border-2 border-white"></span>
                    </button>

                    <!-- Divider -->
                    <div class="hidden lg:block h-10 w-[1px] bg-rose-100" aria-hidden="true"></div>

                    <!-- User Profile -->
                    <div class="flex items-center gap-3">
                        <!-- User Info -->
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-800">Sarah Jenkins</p>
                            <p class="text-[11px] text-slate-400 font-medium">Administrator Utama</p>
                        </div>
                        <!-- Avatar -->
                        <div class="w-10 h-10 rounded-full border-2 border-primary overflow-hidden">
                            <img alt="Sarah Jenkins"
                                 class="w-full h-full object-cover"
                                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuBd5O7bCkEck3mAE5X76r1tzFmTi7vKs91dKvImYSOPIZA6ffLRGC-STIkifTXUM4dKJ9Nvbv-zKUBcIhJS77ZU5eq6mfSxCy9V3P37-VgdAB4HStRt-tHkMDuxTXsSe8QJZPEJk9yiTrOOa_RBPQ97S-m-R8PDN0JC77F2SEUSSf8FGtzZsGatzd963VH6Zt8vP_uYyN737GksaTEQtNYrn0XDnRS_nOBBStzyRCHfmx8K9P9UxcAPPj9aSLEa-G6uVC8w_qVHKdzY"/>
                        </div>
                    </div>

                </div>

            </header>

            <!-- ============================================
                 MAIN CONTENT (Page Content Area)
                 ============================================ -->
            <main id="main-content" class="flex-1 overflow-y-auto bg-white p-6 lg:p-10 space-y-8 custom-scrollbar">

                <!-- ========================================
                     SECTION 1: KPI STAT CARDS

                     DATA KONSISTEN:
                     - Total Pengguna: 12,450
                     - Pengguna Aktif: 8,210 (66%)
                     - Catatan Siklus: 45,200
                     - Total Prediksi: 38,900
                     ======================================== -->
                <section aria-label="Statistik Utama">
                    <h3 class="sr-only">Statistik Dashboard</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                        <!-- CARD 1: Total Pengguna -->
                        <div class="stat-card fade-in delay-1 bg-gradient-to-br from-rose-50 to-white p-6 rounded-2xl border border-rose-100 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                        Total Pengguna
                                    </p>
                                    <!-- DATA: 12,450 pengguna (konsisten dengan Analitik) -->
                                    <p class="text-3xl font-bold text-slate-800" aria-label="12.450 total pengguna">
                                        12,450
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-primary shadow-sm">
                                    <span class="material-symbols-outlined" aria-hidden="true">group</span>
                                </div>
                            </div>
                            <!-- Growth indicator -->
                            <div class="flex items-center gap-1 text-emerald-600 font-bold text-sm">
                                <span class="material-symbols-outlined text-sm" aria-hidden="true">trending_up</span>
                                <span>+5.2%</span>
                                <span class="text-slate-400 font-normal ml-1">vs bulan lalu</span>
                            </div>
                        </div>

                        <!-- CARD 2: Pengguna Aktif -->
                        <div class="stat-card fade-in delay-2 bg-gradient-to-br from-amber-50 to-white p-6 rounded-2xl border border-amber-100 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                        Pengguna Aktif
                                    </p>
                                    <!-- DATA: 8,210 pengguna aktif (66% dari 12,450) -->
                                    <p class="text-3xl font-bold text-slate-800" aria-label="8.210 pengguna aktif">
                                        8,210
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-amber-500 shadow-sm">
                                    <span class="material-symbols-outlined" aria-hidden="true">vital_signs</span>
                                </div>
                            </div>
                            <div class="text-slate-500 font-medium text-sm">
                                Tingkat keterlibatan
                                <span class="font-bold text-slate-800">66%</span>
                            </div>
                        </div>

                        <!-- CARD 3: Catatan Siklus -->
                        <div class="stat-card fade-in delay-3 bg-gradient-to-br from-blue-50 to-white p-6 rounded-2xl border border-blue-100 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                        Catatan Siklus
                                    </p>
                                    <!-- DATA: 45,200 catatan (konsisten dengan Data Siklus) -->
                                    <p class="text-3xl font-bold text-slate-800" aria-label="45.200 catatan siklus">
                                        45,200
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-500 shadow-sm">
                                    <span class="material-symbols-outlined" aria-hidden="true">history_edu</span>
                                </div>
                            </div>
                            <div class="text-slate-500 font-medium text-sm">
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                    Sinkronisasi langsung
                                </span>
                            </div>
                        </div>

                        <!-- CARD 4: Total Prediksi -->
                        <div class="stat-card fade-in delay-4 bg-gradient-to-br from-emerald-50 to-white p-6 rounded-2xl border border-emerald-100 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                        Total Prediksi
                                    </p>
                                    <!-- DATA: 38,900 prediksi (konsisten dengan semua halaman) -->
                                    <p class="text-3xl font-bold text-slate-800" aria-label="38.900 total prediksi">
                                        38,900
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-emerald-500 shadow-sm">
                                    <span class="material-symbols-outlined" aria-hidden="true">magic_button</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-primary font-bold text-sm">
                                <span class="material-symbols-outlined text-sm" aria-hidden="true">verified</span>
                                <!-- DATA: Akurasi 98.2% (konsisten dengan Prediksi) -->
                                <span>Akurasi 98.2%</span>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- ========================================
                     SECTION 2: CHARTS

                     KOMPONEN:
                     - Line Chart: Trend engagement 30 hari
                     - Bar Chart: Volume prediksi per fase
                     ======================================== -->
                <section aria-label="Grafik dan Visualisasi">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        <!-- CHART 1: Line Chart - Trend Engagement -->
                        <div class="fade-in delay-5 lg:col-span-2 bg-white p-8 rounded-2xl border border-rose-100 shadow-sm">
                            <!-- Chart Header -->
                            <div class="flex items-center justify-between mb-8">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">Tren Keterlibatan Sistem</h3>
                                    <p class="text-sm text-slate-400 mt-1">Aktivitas pengguna waktu nyata selama 30 hari</p>
                                </div>
                                <!-- Time range selector -->
                                <select class="bg-slate-50 border-none rounded-lg text-xs font-bold text-slate-500 px-4 py-2 ring-1 ring-rose-50 focus:ring-2 focus:ring-primary/20"
                                        aria-label="Pilih rentang waktu">
                                    <option>30 Hari Terakhir</option>
                                    <option>7 Hari Terakhir</option>
                                    <option>90 Hari Terakhir</option>
                                </select>
                            </div>

                            <!-- SVG Chart -->
                            <div class="h-64 relative" role="img" aria-label="Line chart showing user engagement trend over 30 days">
                                <svg class="w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 100 40">
                                    <!-- Gradient definition -->
                                    <defs>
                                        <linearGradient id="chartGradient" x1="0%" x2="0%" y1="0%" y2="100%">
                                            <stop offset="0%" style="stop-color:#FFB7A5;stop-opacity:0.4"></stop>
                                            <stop offset="100%" style="stop-color:#FFB7A5;stop-opacity:0"></stop>
                                        </linearGradient>
                                    </defs>
                                    <!-- Line path dengan animation -->
                                    <path class="line-animate"
                                          d="M0 35 Q 10 32, 20 28 T 40 15 T 60 25 T 80 10 T 100 5"
                                          fill="none"
                                          stroke="#E35D6A"
                                          stroke-width="0.5"
                                          stroke-linecap="round"
                                          stroke-linejoin="round"></path>
                                    <!-- Area fill -->
                                    <path d="M0 35 Q 10 32, 20 28 T 40 15 T 60 25 T 80 10 T 100 5 L 100 40 L 0 40 Z"
                                          fill="url(#chartGradient)"></path>
                                </svg>
                                <!-- X-axis labels -->
                                <div class="flex justify-between mt-4 text-[10px] text-slate-400 font-bold uppercase tracking-wider" aria-hidden="true">
                                    <span>Hari 1</span>
                                    <span>Hari 7</span>
                                    <span>Hari 14</span>
                                    <span>Hari 21</span>
                                    <span>Hari 30</span>
                                </div>
                            </div>
                        </div>

                        <!-- CHART 2: Bar Chart - Volume Prediksi per Fase -->
                        <div class="fade-in delay-6 bg-white p-8 rounded-2xl border border-rose-100 shadow-sm flex flex-col">
                            <div class="mb-8">
                                <h3 class="text-lg font-bold text-slate-800">Volume Prediksi</h3>
                                <p class="text-sm text-slate-400 mt-1">Distribusi berdasarkan fase siklus</p>
                            </div>

                            <!-- Bar Chart Container -->
                            <div class="flex-1 flex items-end justify-between px-4 pb-2 h-64" role="img" aria-label="Bar chart showing prediction volume by cycle phase">

                                <!-- BAR: Folikel -->
                                <!-- DATA: 9,420 (konsisten semua halaman) -->
                                <div class="flex flex-col items-center flex-1 h-full justify-end">
                                    <span class="text-xs font-bold text-primary mb-2">9,420</span>
                                    <div class="w-12 bg-rose-50 rounded-t-2xl relative overflow-hidden transition-all duration-300 hover:bg-rose-100"
                                         style="height: 60%;"
                                         data-tooltip="Fase Folikel: 9,420 prediksi">
                                        <div class="absolute bottom-0 w-full bg-primary rounded-t-2xl h-full"></div>
                                    </div>
                                    <span class="mt-4 text-[10px] text-slate-500 font-bold tracking-widest">FOLIK</span>
                                </div>

                                <!-- BAR: Ovulasi -->
                                <!-- DATA: 14,200 (konsisten semua halaman) -->
                                <div class="flex flex-col items-center flex-1 h-full justify-end">
                                    <span class="text-xs font-bold text-primary mb-2">14,200</span>
                                    <div class="w-12 bg-rose-50 rounded-t-2xl relative overflow-hidden transition-all duration-300 hover:bg-rose-100"
                                         style="height: 90%;"
                                         data-tooltip="Fase Ovulasi: 14,200 prediksi">
                                        <div class="absolute bottom-0 w-full bg-primary rounded-t-2xl h-full"></div>
                                    </div>
                                    <span class="mt-4 text-[10px] text-slate-500 font-bold tracking-widest">OVUL</span>
                                </div>

                                <!-- BAR: Luteal -->
                                <!-- DATA: 11,150 (konsisten semua halaman) -->
                                <div class="flex flex-col items-center flex-1 h-full justify-end">
                                    <span class="text-xs font-bold text-primary mb-2">11,150</span>
                                    <div class="w-12 bg-rose-50 rounded-t-2xl relative overflow-hidden transition-all duration-300 hover:bg-rose-100"
                                         style="height: 75%;"
                                         data-tooltip="Fase Luteal: 11,150 prediksi">
                                        <div class="absolute bottom-0 w-full bg-primary rounded-t-2xl h-full"></div>
                                    </div>
                                    <span class="mt-4 text-[10px] text-slate-500 font-bold tracking-widest">LUT</span>
                                </div>

                                <!-- BAR: Menstruasi -->
                                <!-- DATA: 4,130 (konsisten semua halaman) -->
                                <div class="flex flex-col items-center flex-1 h-full justify-end">
                                    <span class="text-xs font-bold text-primary mb-2">4,130</span>
                                    <div class="w-12 bg-rose-50 rounded-t-2xl relative overflow-hidden transition-all duration-300 hover:bg-rose-100"
                                         style="height: 30%;"
                                         data-tooltip="Fase Menstruasi: 4,130 prediksi">
                                        <div class="absolute bottom-0 w-full bg-primary rounded-t-2xl h-full"></div>
                                    </div>
                                    <span class="mt-4 text-[10px] text-slate-500 font-bold tracking-widest">MENS</span>
                                </div>

                            </div>

                            <!-- Last updated timestamp -->
                            <p class="mt-8 text-center text-xs text-slate-400 italic font-medium">
                                Diperbarui hari ini, 08:00 WIB
                            </p>
                        </div>

                    </div>
                </section>

                <!-- ========================================
                     SECTION 3: ACTIVITY LOG TABLE

                     KOMPONEN:
                     - Real-time activity log
                     - Event types dengan icon
                     - Status badges
                     - Action buttons
                     ======================================== -->
                <section class="fade-in delay-6 bg-white border border-rose-100 rounded-2xl overflow-hidden shadow-sm"
                         aria-label="Log Aktivitas Terbaru">

                    <!-- Table Header -->
                    <div class="p-8 flex items-center justify-between border-b border-rose-50">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Log Aktivitas Terbaru</h3>
                            <p class="text-sm text-slate-400 mt-1">Aktivitas sistem dalam waktu nyata</p>
                        </div>
                        <button class="text-primary text-sm font-bold hover:underline focus:underline transition-all">
                            Lihat Laporan Performa
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <!-- Table Header -->
                            <thead class="bg-rose-50/30 text-[11px] uppercase tracking-widest text-slate-500 font-bold">
                                <tr>
                                    <th scope="col" class="px-8 py-4">Tipe Kejadian</th>
                                    <th scope="col" class="px-8 py-4">Identitas Pengguna</th>
                                    <th scope="col" class="px-8 py-4">Status</th>
                                    <th scope="col" class="px-8 py-4">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rose-50 text-sm">

                                <!-- Row 1: Siklus Diprediksi -->
                                <tr class="table-row-hover">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <span class="material-icons-round text-emerald-500" aria-hidden="true">check_circle</span>
                                            <div>
                                                <p class="font-semibold text-slate-800">Siklus Diprediksi</p>
                                                <p class="text-[10px] text-slate-400 uppercase">
                                                    <time datetime="2024-02-14T14:58:00">2 menit yang lalu</time>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <code class="font-mono text-xs text-slate-500">USR_88219_GLOBAL</code>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase rounded-full border border-emerald-100">
                                            Berhasil
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <button class="text-slate-400 hover:text-primary p-2 rounded-lg hover:bg-rose-50 transition-all"
                                                aria-label="More actions">
                                            <span class="material-icons-round">more_vert</span>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 2: Pendaftaran Pengguna -->
                                <tr class="table-row-hover">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <span class="material-icons-round text-blue-500" aria-hidden="true">person_add</span>
                                            <div>
                                                <p class="font-semibold text-slate-800">Pendaftaran Pengguna</p>
                                                <p class="text-[10px] text-slate-400 uppercase">
                                                    <time datetime="2024-02-14T14:45:00">15 menit yang lalu</time>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <code class="font-mono text-xs text-slate-500">USR_88220_NEW</code>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase rounded-full border border-blue-100">
                                            Menunggu
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <button class="text-slate-400 hover:text-primary p-2 rounded-lg hover:bg-rose-50 transition-all"
                                                aria-label="More actions">
                                            <span class="material-icons-round">more_vert</span>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Row 3: Anomali Prediksi -->
                                <tr class="table-row-hover">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <span class="material-icons-round text-primary" aria-hidden="true">report_problem</span>
                                            <div>
                                                <p class="font-semibold text-slate-800">Anomali Prediksi</p>
                                                <p class="text-[10px] text-slate-400 uppercase">
                                                    <time datetime="2024-02-14T13:00:00">1 jam yang lalu</time>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <code class="font-mono text-xs text-slate-500">USR_44120_ALERT</code>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="px-3 py-1 bg-rose-50 text-primary text-[10px] font-bold uppercase rounded-full border border-rose-100">
                                            Peringatan
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <button class="text-slate-400 hover:text-primary p-2 rounded-lg hover:bg-rose-50 transition-all"
                                                aria-label="More actions">
                                            <span class="material-icons-round">more_vert</span>
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </section>

            </main>

        </div>

    </div>

    <!-- ============================================
         JAVASCRIPT FUNCTIONALITY

         CATATAN:
         - Semua function diberi komentar lengkap
         - Error handling included
         - Responsive behavior handled
         - Keyboard navigation support
         ============================================ -->
    <script>
        /* ========================================
           GLOBAL STATE & CONSTANTS
           ======================================== */
        const APP_CONFIG = {
            ANIMATION_DURATION: 300,
            MOBILE_BREAKPOINT: 1024,
        };

        const AppState = {
            sidebarOpen: window.innerWidth >= APP_CONFIG.MOBILE_BREAKPOINT,
        };

        /* ========================================
           SIDEBAR TOGGLE (Mobile)

           FUNGSI:
           - Toggle sidebar visibility di mobile
           - Update overlay backdrop
           - Set ARIA attributes untuk accessibility
           ======================================== */
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            const menuButton = document.querySelector('[aria-label="Toggle menu"]');

            AppState.sidebarOpen = !AppState.sidebarOpen;

            if (AppState.sidebarOpen) {
                sidebar.classList.add('open');
                overlay.classList.add('active');
                menuButton?.setAttribute('aria-expanded', 'true');
                // Prevent body scroll saat sidebar open
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                menuButton?.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        }

        /* ========================================
           RESPONSIVE HANDLING

           FUNGSI:
           - Auto-adjust sidebar saat window resize
           - Debounced untuk performance
           ======================================== */
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const isDesktop = window.innerWidth >= APP_CONFIG.MOBILE_BREAKPOINT;
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('mobileOverlay');

                if (isDesktop) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                    AppState.sidebarOpen = true;
                } else if (!AppState.sidebarOpen) {
                    sidebar.classList.remove('open');
                }
            }, 150);
        });

        /* ========================================
           LOGOUT HANDLER

           FUNGSI:
           - Konfirmasi logout
           - Redirect ke logout endpoint
           ======================================== */
        function handleLogout() {
            if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                // TODO: Implement actual logout logic
                console.log('Logging out...');
                // window.location.href = '/api/logout';
            }
        }

        /* ========================================
           KEYBOARD NAVIGATION

           FUNGSI:
           - ESC key closes sidebar di mobile
           ======================================== */
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && AppState.sidebarOpen && window.innerWidth < APP_CONFIG.MOBILE_BREAKPOINT) {
                toggleSidebar();
            }
        });

        /* ========================================
           UTILITY FUNCTIONS
           ======================================== */

        /**
         * Format number dengan Indonesian locale
         * @param {number} num - Number to format
         * @returns {string} Formatted number (e.g., "12,450")
         */
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        /**
         * Format date dengan Indonesian locale
         * @param {Date|string} date - Date to format
         * @returns {string} Formatted date
         */
        function formatDate(date) {
            return new Intl.DateTimeFormat('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }).format(new Date(date));
        }

        /**
         * Show toast notification
         * @param {string} message - Message to display
         * @param {string} type - Type: success, error, warning, info
         */
        function showToast(message, type = 'info') {
            // TODO: Implement toast notification system
            console.log(`[${type.toUpperCase()}] ${message}`);
        }

        /* ========================================
           INITIALIZE ON DOM READY
           ======================================== */
        document.addEventListener('DOMContentLoaded', () => {
            // Set initial sidebar state untuk mobile
            if (window.innerWidth < APP_CONFIG.MOBILE_BREAKPOINT) {
                document.getElementById('sidebar').classList.remove('open');
                AppState.sidebarOpen = false;
            }

            // Log successful initialization
            console.log('✅ MIRAI Dashboard initialized successfully');

            // Announce page load to screen readers
            const announcement = document.createElement('div');
            announcement.setAttribute('role', 'status');
            announcement.setAttribute('aria-live', 'polite');
            announcement.className = 'sr-only';
            announcement.textContent = 'Dashboard loaded successfully';
            document.body.appendChild(announcement);
        });

        /* ========================================
           PERFORMANCE MONITORING (Optional)
           ======================================== */
        if (window.performance) {
            window.addEventListener('load', () => {
                const perfData = window.performance.timing;
                const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
                console.log(`📊 Page load time: ${pageLoadTime}ms`);
            });
        }
    </script>

</body>
</html>
