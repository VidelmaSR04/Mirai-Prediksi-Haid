{{-- ===================== FOOTER ===================== --}}
<footer class="bg-baby-pink py-16 text-gray-800 dark:text-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Footer Content Grid --}}
        <div class="grid md:grid-cols-4 gap-12 mb-12">

            {{-- Brand & Description --}}
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-3 mb-6">
                    <img alt="Logo" class="h-8 w-8 object-contain" src="{{ asset('img/logo fix.png') }}" />
                    <span class="font-display font-bold text-xl tracking-tight text-primary">MIRAI</span>
                </div>
                <p class="text-gray-600 max-w-sm">
                    MIRAI adalah aplikasi pendamping siklus menstruasi yang membantu pengguna mencatat riwayat siklus, memperoleh perkiraan tanggal menstruasi berikutnya, serta mendapatkan informasi seputar kesehatan reproduksi wanita.
                </p>
            </div>

            {{-- Navigasi --}}
            <div>
                <h5 class="font-bold mb-6">Navigasi</h5>
                <ul class="space-y-4 text-gray-600">
                    <li><a class="hover:text-primary transition-colors" href="#home">Beranda</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#about">Tentang</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#features">Fitur</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#cta">Unduh Aplikasi</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#team">Tim</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#contact">Kontak</a></li>
                </ul>
            </div>

            {{-- Informasi --}}
            <div>
                <h5 class="font-bold mb-6">Informasi</h5>
                <ul class="space-y-4 text-gray-600">
                    <li><a class="hover:text-primary transition-colors" href="{{ route('privacy') }}">Kebijakan Privasi</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#faq">FAQ</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#contact">Hubungi Kami</a></li>
                </ul>
            </div>

        </div>

        {{-- Copyright --}}
        <div class="pt-8 border-t border-primary/10 text-center text-gray-500 text-sm">
            © {{ date('Y') }} MIRAI. Seluruh hak cipta dilindungi.
        </div>

    </div>
</footer>
