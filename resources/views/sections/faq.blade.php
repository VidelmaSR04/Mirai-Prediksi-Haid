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
                    Mirai adalah aplikasi pendamping siklus menstruasi berbasis AI yang membantu mencatat, memahami, dan memprediksi siklus secara personal.
                </div>
            </div>

            {{-- FAQ Item 2 --}}
            <div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
                <button onclick="toggleFAQ(2)" class="w-full flex justify-between items-center p-6 text-left">
                    <span class="font-semibold text-lg">
                        Bagaimana Machine Learning bekerja di Mirai?
                    </span>
                    <span id="icon-2" class="text-2xl text-pink-500 dark:text-pink-400">
                        ♡
                    </span>
                </button>
                <div id="faq-2" class="hidden px-6 pb-6 text-gray-600 dark:text-gray-400">
                    Mirai mempelajari data siklus, gejala, suasana hati, dan ritme tubuh dari waktu ke waktu untuk menghasilkan prediksi yang semakin akurat.
                </div>
            </div>

            {{-- FAQ Item 3 --}}
            <div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
                <button onclick="toggleFAQ(3)" class="w-full flex justify-between items-center p-6 text-left">
                    <span class="font-semibold text-lg">
                        Apakah data pribadiku aman?
                    </span>
                    <span id="icon-3" class="text-2xl text-pink-500 dark:text-pink-400">
                        ♡
                    </span>
                </button>
                <div id="faq-3" class="hidden px-6 pb-6 text-gray-600 dark:text-gray-400">
                    Ya. Data kamu dienkripsi, bersifat privat, dan sepenuhnya milikmu. Mirai tidak membagikan data ke pihak mana pun.
                </div>
            </div>

            {{-- FAQ Item 4 --}}
            <div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
                <button onclick="toggleFAQ(4)" class="w-full flex justify-between items-center p-6 text-left">
                    <span class="font-semibold text-lg">
                        Apakah Mirai memiliki asisten chatbot?
                    </span>
                    <span id="icon-4" class="text-2xl text-pink-500 dark:text-pink-400">
                        ♡
                    </span>
                </button>
                <div id="faq-4" class="hidden px-6 pb-6 text-gray-600 dark:text-gray-400">
                    Ya. Mirai memiliki chatbot AI yang siap membantu menjawab pertanyaan seputar siklus dan kesehatan dengan bahasa yang ramah.
                </div>
            </div>

            {{-- FAQ Item 5 --}}
            <div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
                <button onclick="toggleFAQ(5)" class="w-full flex justify-between items-center p-6 text-left">
                    <span class="font-semibold text-lg">
                        Kapan Mirai bisa diunduh?
                    </span>
                    <span id="icon-5" class="text-2xl text-pink-500 dark:text-pink-400">
                        ♡
                    </span>
                </button>
                <div id="faq-5" class="hidden px-6 pb-6 text-gray-600 dark:text-gray-400">
                    Mirai akan segera hadir di App Store dan Play Store. Tunggu kabar baiknya ya 🌸
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