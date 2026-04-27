{{-- ===================== TEAM SECTION ===================== --}}
<section class="py-24" id="team">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Heading --}}
        <div class="text-center mb-16">
            <h2 class="font-display text-4xl font-bold mb-4">Meet the Visionaries</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Tim pengembang di balik aplikasi prediksi siklus cerdas Mirai.
            </p>
        </div>

        {{-- Team Grid --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Team Member 1: Videlma --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/5 text-center group">
                {{-- Profile Image --}}
                <div class="w-32 h-32 mx-auto mb-6 rounded-full overflow-hidden border-4 border-baby-pink">
                    <img 
                        src="{{ asset('img/videng.jpeg') }}" 
                        alt="Videlma" 
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform" />
                </div>
                
                {{-- Member Info --}}
                <h4 class="text-lg font-bold">Videlma</h4>
                <p class="text-primary text-sm font-semibold mb-3">NIM: E3124 | UI/UX Designer & Machine Learning</p>
                <p class="text-gray-500 text-sm">
                    Bertanggung jawab merancang tampilan antarmuka yang ramah pengguna serta berkontribusi dalam pengembangan model Machine Learning pada Mirai.
                </p>
            </div>

            {{-- Team Member 2: Adinda Riski --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/5 text-center group">
                {{-- Profile Image --}}
                <div class="w-32 h-32 mx-auto mb-6 rounded-full overflow-hidden border-4 border-soft-mint">
                    <img 
                        alt="Adinda Riski" 
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform" 
                        src="{{ asset('img/dinda.jpeg') }}"  />
                </div>
                
                {{-- Member Info --}}
                <h4 class="text-lg font-bold">Adinda Riski</h4>
                <p class="text-primary text-sm font-semibold mb-3">NIM: E3124 | Front End Web & Mobile</p>
                <p class="text-gray-500 text-sm">
                    Mengembangkan tampilan web dan aplikasi mobile agar responsif, interaktif, dan sesuai dengan desain yang telah ditentukan.
                </p>
            </div>

            {{-- Team Member 3: Risky Triana --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/5 text-center group">
                {{-- Profile Image --}}
                <div class="w-32 h-32 mx-auto mb-6 rounded-full overflow-hidden border-4 border-peach-pink">
                    <img 
                        alt="Risky Triana" 
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform" 
                        src="{{ asset('img/putri.jpeg') }}"  />
                </div>
                
                {{-- Member Info --}}
                <h4 class="text-lg font-bold">Risky Triana</h4>
                <p class="text-primary text-sm font-semibold mb-3">NIM: E3124 | Back End & Machine Learning</p>
                <p class="text-gray-500 text-sm">
                    Mengelola logika server, basis data, serta mendukung implementasi Machine Learning untuk memastikan sistem berjalan optimal.
                </p>
            </div>

            {{-- Team Member 4: Revina --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/5 text-center group">
                {{-- Profile Image --}}
                <div class="w-32 h-32 mx-auto mb-6 rounded-full overflow-hidden border-4 border-baby-pink">
                    <img 
                        alt="Revina" 
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform" 
                        src="{{ asset('img/vina.jpeg') }}"  />
                </div>
                
                {{-- Member Info --}}
                <h4 class="text-lg font-bold">Revina</h4>
                <p class="text-primary text-sm font-semibold mb-3">NIM: E3124 | Asisten Front End</p>
                <p class="text-gray-500 text-sm">
                    Mendukung pengembangan Front End dengan membantu implementasi komponen UI dan pengujian tampilan aplikasi.
                </p>
            </div>

        </div>
    </div>
</section>