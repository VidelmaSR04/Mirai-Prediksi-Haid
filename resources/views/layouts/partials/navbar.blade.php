{{-- ===================== NAVBAR ===================== --}}
<div class="fixed top-4 left-0 w-full z-50 px-4 sm:px-6 lg:px-8">
    <nav class="max-w-6xl mx-auto glass-nav rounded-full shadow-xl shadow-baby-pink/20 overflow-hidden">
        <div class="px-6 py-3 flex justify-between items-center">

            {{-- Logo Section --}}
            <div class="flex items-center gap-2 group cursor-pointer">
                <div class="w-10 h-10 bg-baby-pink rounded-full flex items-center justify-center group-hover:rotate-12 transition-transform">
                    <img src="{{ asset('img/logo fix.png') }}" alt="Mirai Logo" class="w-full h-auto" />
                </div>
                <span class="font-display font-bold text-xl tracking-tight text-primary">
                    MIRAI
                </span>
            </div>

            {{-- Navigation Menu --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ url('/') }}" class="px-5 py-2 rounded-full font-bold text-sm hover:bg-baby-pink/30 transition-all {{ Request::is('/') ? 'bg-baby-pink/30' : '' }}">
                    Home
                </a>
                <a href="#about" class="px-5 py-2 rounded-full font-bold text-sm hover:bg-baby-pink/30 transition-all">
                    About
                </a>
                <a href="#team" class="px-5 py-2 rounded-full font-bold text-sm hover:bg-baby-pink/30 transition-all">
                    Team
                </a>
                <a href="#contact" class="px-5 py-2 rounded-full font-bold text-sm hover:bg-baby-pink/30 transition-all">
                    Contact
                </a>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3">
                {{-- Dark Mode Toggle --}}
                <button class="p-2 rounded-full bg-soft-mint/50 dark:bg-gray-800 text-primary dark:text-baby-pink hover:scale-110 transition-transform" onclick="document.documentElement.classList.toggle('dark')">
                    <span class="material-symbols-outlined">dark_mode</span>
                </button>

                {{-- Download Button --}}
                <button class="bg-primary text-white px-7 py-2.5 rounded-full font-bold text-sm transition-all shadow-lg shadow-primary/25 active:scale-95">
                    Download
                </button>
            </div>

        </div>
    </nav>
</div>