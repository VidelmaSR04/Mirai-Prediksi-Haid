{{-- ===================== FAQ SECTION ===================== --}}
<section class="py-24 bg-white dark:bg-gray-900" id="faq">
    <div class="max-w-4xl mx-auto px-4">

        {{-- Section Heading --}}
        <div class="text-center mb-16">
            <h2 class="font-display text-4xl font-bold mb-4">
                Pertanyaan yang Sering Diajukan
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                Semua yang perlu kamu tahu tentang Mirai.
            </p>
        </div>

        {{-- FAQ List --}}
        <div class="space-y-4">

           {{-- FAQ Item 1 --}}
<div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <button onclick="toggleFAQ(1)" class="w-full flex justify-between items-center p-6 text-left">
        <span class="font-semibold text-lg">
            Apa itu aplikasi Mirai?
        </span>
        <span id="icon-1" class="text-2xl text-pink-500 dark:text-pink-400">
            ♡
        </span>
    </button>
    <div id="faq-1" class="hidden px-6 pb-6 text-gray-600 dark:text-gray-400">
        Mirai adalah aplikasi yang membantu mencatat siklus menstruasi serta memberikan perkiraan tanggal menstruasi berikutnya berdasarkan riwayat siklus yang telah dicatat pengguna.
    </div>
</div>

{{-- FAQ Item 2 --}}
<div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <button onclick="toggleFAQ(2)" class="w-full flex justify-between items-center p-6 text-left">
        <span class="font-semibold text-lg">
            Bagaimana Mirai memperkirakan tanggal menstruasi?
        </span>
        <span id="icon-2" class="text-2xl text-pink-500 dark:text-pink-400">
            ♡
        </span>
    </button>
    <div id="faq-2" class="hidden px-6 pb-6 text-gray-600 dark:text-gray-400">
        Mirai menggunakan data riwayat menstruasi dan panjang siklus yang dicatat pengguna untuk membantu memperkirakan tanggal menstruasi berikutnya secara lebih personal.
    </div>
</div>

{{-- FAQ Item 3 --}}
<div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <button onclick="toggleFAQ(3)" class="w-full flex justify-between items-center p-6 text-left">
        <span class="font-semibold text-lg">
            Apakah perkiraan tanggal menstruasi selalu tepat?
        </span>
        <span id="icon-3" class="text-2xl text-pink-500 dark:text-pink-400">
            ♡
        </span>
    </button>
    <div id="faq-3" class="hidden px-6 pb-6 text-gray-600 dark:text-gray-400">
        Perkiraan yang diberikan Mirai dapat membantu pengguna memahami pola siklusnya, namun hasilnya dapat berbeda karena setiap tubuh memiliki kondisi yang unik dan dapat dipengaruhi oleh berbagai faktor seperti stres, pola tidur, maupun gaya hidup.
    </div>
</div>

{{-- FAQ Item 4 --}}
<div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <button onclick="toggleFAQ(4)" class="w-full flex justify-between items-center p-6 text-left">
        <span class="font-semibold text-lg">
            Apa fungsi chatbot di Mirai?
        </span>
        <span id="icon-4" class="text-2xl text-pink-500 dark:text-pink-400">
            ♡
        </span>
    </button>
    <div id="faq-4" class="hidden px-6 pb-6 text-gray-600 dark:text-gray-400">
        Chatbot Mirai membantu menjawab pertanyaan seputar siklus menstruasi, gejala yang dirasakan, serta memberikan informasi dan saran pola hidup sehat yang mudah dipahami.
    </div>
</div>

{{-- FAQ Item 5 --}}
<div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
    <button onclick="toggleFAQ(5)" class="w-full flex justify-between items-center p-6 text-left">
        <span class="font-semibold text-lg">
            Apakah data pribadiku aman?
        </span>
        <span id="icon-5" class="text-2xl text-pink-500 dark:text-pink-400">
            ♡
        </span>
    </button>
    <div id="faq-5" class="hidden px-6 pb-6 text-gray-600 dark:text-gray-400">
        Ya. Data yang kamu masukkan disimpan dengan aman dan hanya digunakan untuk membantu pencatatan serta perkiraan siklus menstruasi. Data pribadi tidak dibagikan kepada pihak lain tanpa izin pengguna.
    </div>
</div>
        </div>
    </div>
</section>

{{-- FAQ Toggle Script --}}
@push('scripts')
<script>
    function toggleFAQ(id) {
        const content = document.getElementById(`faq-${id}`);
        const icon = document.getElementById(`icon-${id}`);

        if (content.classList.contains("hidden")) {
            // Buka FAQ
            content.classList.remove("hidden");
            icon.textContent = "♥";
        } else {
            // Tutup FAQ
            content.classList.add("hidden");
            icon.textContent = "♡";
        }
    }
</script>
@endpush
