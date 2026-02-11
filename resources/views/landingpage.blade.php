<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<!-- head landing -->
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Mirai | Index</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Poppins:wght@400;600;700&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>

    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#E35D6A",
                        "baby-pink": "#FFD1DC",
                        "peach-pink": "#FFB7A5",
                        "soft-mint": "#D8EFE6",
                        "background-light": "#FFFFFF",
                        "background-dark": "#1A1A1A",
                    },
                    fontFamily: {
                        sans: ["Nunito", "sans-serif"],
                        display: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "1rem",
                        xl: "1.5rem",
                        "2xl": "2rem",
                        "3xl": "2.5rem",
                    },
                },
            },
        };
    </script>

    <!-- Custom Tailwind Components -->
    <style type="text/tailwindcss">
        @layer components {
            .glass-nav {
                @apply bg-white/70 backdrop-blur-md border border-white/20;
            }

            .dark .glass-nav {
                @apply bg-black/40 backdrop-blur-md border border-white/5;
            }

            .organic-shape {
                border-radius: 60% 40% 70% 30% / 30% 60% 40% 70%;
            }

            .hero-gradient {
                background: linear-gradient(135deg, #FFD1DC 0%, #FFB7A5 100%);
            }
        }
    </style>
</head>

<body
    class="font-sans bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-100 transition-colors duration-300">

    <!-- ===================== NAVBAR ===================== -->
    <div class="fixed top-4 left-0 w-full z-50 px-4 sm:px-6 lg:px-8">
        <nav class="max-w-6xl mx-auto glass-nav rounded-full shadow-xl shadow-baby-pink/20 overflow-hidden">
            <div class="px-6 py-3 flex justify-between items-center">

                <!-- Logo -->
                <div class="flex items-center gap-2 group cursor-pointer">
                    <div
                        class="w-10 h-10 bg-baby-pink rounded-full flex items-center justify-center group-hover:rotate-12 transition-transform">
                      <img
                        src="img/logo fix.png"
                        alt="App Interface Mockup"
                        class="w-full h-auto" />
                    </div>
                    <span class="font-display font-bold text-xl tracking-tight text-primary">
                        MIRAI
                    </span>
                </div>

                <!-- Navigation Menu -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="#"
                        class="px-5 py-2 rounded-full font-bold text-sm hover:bg-baby-pink/30 transition-all">
                        Home
                    </a>
                    <a href="#about"
                        class="px-5 py-2 rounded-full font-bold text-sm hover:bg-baby-pink/30 transition-all">
                        About
                    </a>
                    <a href="#team"
                        class="px-5 py-2 rounded-full font-bold text-sm hover:bg-baby-pink/30 transition-all">
                        Team
                    </a>
                    <a href="#contact"
                        class="px-5 py-2 rounded-full font-bold text-sm hover:bg-baby-pink/30 transition-all">
                        Contact
                    </a>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <button
                        class="p-2 rounded-full bg-soft-mint/50 dark:bg-gray-800 text-primary dark:text-baby-pink hover:scale-110 transition-transform"
                        onclick="document.documentElement.classList.toggle('dark')">
                        <span class="material-symbols-outlined">dark_mode</span>
                    </button>

                    <button
                        class="bg-primary text-white px-7 py-2.5 rounded-full font-bold text-sm transition-all shadow-lg shadow-primary/25 active:scale-95">
                        Download
                    </button>
                </div>

            </div>
        </nav>
    </div>

    <!-- ===================== HOME / HERO SECTION ===================== -->
<section class="relative pt-40 pb-20 lg:pt-52 lg:pb-32 overflow-hidden">
<div class="absolute top-0 right-0 -z-10 w-1/2 h-full bg-soft-mint/30 organic-shape scale-150 translate-x-1/4 -translate-y-1/4 hidden lg:block"></div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex flex-col lg:flex-row items-center gap-12">
<div class="lg:w-1/2 text-center lg:text-left">
<span class="inline-block px-4 py-1.5 mb-6 text-sm font-bold tracking-wider text-primary uppercase bg-baby-pink/30 rounded-full">
                    Presisi Siklus Berbasis AI
                </span>
<h1 class="font-display text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    Dipersonalisasi secara cerdas untuk <span class="text-primary">memahami ritme </span> alami tubuhmu.
                </h1>
<p class="text-lg lg:text-xl text-gray-600 dark:text-gray-400 mb-10 max-w-lg mx-auto lg:mx-0">
                    Dibantu teknologi yang bekerja perlahan di balik layar, menemanimu memahami siklus dengan lebih tenang.
                </p>
<div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
<button class="bg-primary text-white px-10 py-4 rounded-full font-bold text-lg hover:opacity-90 transition-all shadow-xl shadow-primary/30 flex items-center gap-2">
<span class="material-symbols-outlined">download</span>
                        Download Now
                    </button>
<button class="px-10 py-4 rounded-full font-bold text-lg border-2 border-primary text-primary hover:bg-primary/5 transition-all">
                        Learn More
                    </button>
</div>
</div>
<div class="lg:w-1/2 relative">
<div class="relative z-10 hero-gradient p-8 rounded-[3rem] shadow-2xl rotate-3 hover:rotate-0 transition-transform duration-500">
<div class="bg-white dark:bg-gray-900 rounded-[2rem] overflow-hidden border-8 border-white dark:border-gray-800 shadow-inner">
<img alt="App Interface Mockup" class="w-full h-auto" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCiq8BQBuKqzzgQ4sW9aE28gumKbCq_taSikBoVjvFQXA9jKjA45PoSdc1-lDUGzF3w_QuL1gwVynmuy5ROr0eykbIElxWz6WL5I6LcMy9rQy3zmKkwQ-o8LQwHe9WaW1LAzet_Ec0tAb4wwTKPPYPpir4qjS8HYInn1QJjyybLPcPUUiLgwkh6nna_PW-moDqQ36kRj7ANEYBATf6SrO1g8I0v7Noi_GFt6WkqTxA4NVBpwfhcIYsh2aD5kzajDeB2ymV29Yl1s1J5"/>
</div>
</div>
<div class="absolute -top-10 -right-10 w-32 h-32 bg-soft-mint rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
<div class="absolute -bottom-10 -left-10 w-40 h-40 bg-baby-pink rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse delay-700"></div>
</div>
</div>
</div>
</section>

    <!-- ===================== ABOUT SECTION ===================== -->
 <section class="py-24 bg-white dark:bg-[#1f1f1f]" id="about">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-20">
<h2 class="font-display text-4xl font-bold mb-4">Mengapa Mirai Cocok Sebagai Pilihan Anda?</h2>
<div class="w-20 h-1.5 bg-primary mx-auto rounded-full"></div>
</div>
<div class="grid md:grid-cols-2 gap-16 items-center">
<div class="space-y-8">

    <!-- 1. Cerdas & Personal -->
    <div class="flex gap-6 items-start">
        <div class="flex-shrink-0 w-14 h-14 bg-soft-mint rounded-2xl flex items-center justify-center text-primary shadow-sm">
            <span class="material-symbols-outlined text-3xl">psychology</span>
        </div>
        <div>
            <h3 class="text-xl font-bold mb-2">Cerdas & Personal</h3>
            <p class="text-gray-600 dark:text-gray-400">
                Mirai memanfaatkan Machine Learning untuk mempelajari data siklus, gejala, dan ritme tubuhmu dari waktu ke waktu, sehingga prediksi menjadi semakin akurat dan personal.
            </p>
        </div>
    </div>

    <!-- 2. Prediksi Berbasis Machine Learning -->
    <div class="flex gap-6 items-start">
        <div class="flex-shrink-0 w-14 h-14 bg-peach-pink/20 rounded-2xl flex items-center justify-center text-primary shadow-sm">
            <span class="material-symbols-outlined text-3xl">hub</span>
        </div>
        <div>
            <h3 class="text-xl font-bold mb-2">Prediksi Berbasis Machine Learning</h3>
            <p class="text-gray-600 dark:text-gray-400">
                Model Machine Learning Mirai menganalisis pola historis untuk memberikan estimasi siklus yang lebih presisi dibandingkan pencatatan manual biasa.
            </p>
        </div>
    </div>

    <!-- 3. Asisten Pintar -->
    <div class="flex gap-6 items-start">
        <div class="flex-shrink-0 w-14 h-14 bg-baby-pink rounded-2xl flex items-center justify-center text-primary shadow-sm">
            <span class="material-symbols-outlined text-3xl">chat</span>
        </div>
        <div>
            <h3 class="text-xl font-bold mb-2">Asisten Pintar</h3>
            <p class="text-gray-600 dark:text-gray-400">
                Chatbot berbasis AI membantu menjawab pertanyaan, menjelaskan prediksi, dan menemanimu memahami kondisi tubuh dengan bahasa yang lembut.
            </p>
        </div>
    </div>

    <!-- 4. Lembut & Mudah Digunakan -->
    <div class="flex gap-6 items-start">
        <div class="flex-shrink-0 w-14 h-14 bg-soft-mint/40 rounded-2xl flex items-center justify-center text-primary shadow-sm">
            <span class="material-symbols-outlined text-3xl">calendar_month</span>
        </div>
        <div>
            <h3 class="text-xl font-bold mb-2">Lembut & Mudah Digunakan</h3>
            <p class="text-gray-600 dark:text-gray-400">
                Antarmuka yang sederhana dan menenangkan memudahkan pencatatan siklus, suasana hati, dan kondisi tubuh setiap hari tanpa terasa rumit.
            </p>
        </div>
    </div>

    <!-- 5. Aman & Terpercaya -->
    <div class="flex gap-6 items-start">
        <div class="flex-shrink-0 w-14 h-14 bg-peach-pink/30 rounded-2xl flex items-center justify-center text-primary shadow-sm">
            <span class="material-symbols-outlined text-3xl">lock</span>
        </div>
        <div>
            <h3 class="text-xl font-bold mb-2">Aman & Terpercaya</h3>
            <p class="text-gray-600 dark:text-gray-400">
                Data kesehatanmu dienkripsi, bersifat pribadi, dan sepenuhnya berada dalam kendalimu tanpa dibagikan ke pihak ketiga.
            </p>
        </div>
    </div>

</div>

<div class="grid grid-cols-2 gap-4">
<img alt="Data Analytics" class="rounded-3xl shadow-lg mt-8" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDm-eTQ0-zgGu7mN2e4Cu8vLmtY6WFmG0-VeDVM9l4oOaU5YzPlMbhtnbuCixtPp0DEi69m136XHjUf2jAHWfwbOb0y9f-qBghrgfWRGFFkWbVJskiBoo8ReN-jRPujkc1jPnR9FxdFIYCYktZMzkbsyehGxPq4WpxlnAkFZZ7Q7EogdZNSvCxrqzl2sm2sC__gVqcgL83GXtkRt-yGqX1eViO8od7AOsLFbNNc-oh6rDq0a6me8DEjZV2vkBQR9Coq5VIFoz5ZXwmy"/>
<img alt="Women Wellness" class="rounded-3xl shadow-lg -mt-8" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC94TAbvq9cmptz7PA8Qr9F4yhPqcfHN4vvI8_Jo4riP0vm8B-d4bN-K82l_TLigRQqZ-a_XGVRxyYw3NzHTKNVkjOMsRHmD0kV3-l39kLMzqGq9RJrMP1hzDTi9s90wMPwD6uBcsSOAb_WRejkOUINNtJbqiGDI32PzRX4IxGk8YWYvGrxRMp-RNuUWEFNP2CMOxXuuiDG5jFiTnQVK4GrFYbOWg47XOMah5A0ADgI9H6Gn4-Eu-VWJu8XyCDD2KTs7XtpBrU_aFVg"/>
</div>
</div>
</div>
</section>
    <!-- ===================== CTA SECTION ===================== -->
 <section class="py-20 relative overflow-hidden">
<div class="max-w-5xl mx-auto px-4">
<div class="bg-primary rounded-[3rem] p-12 lg:p-20 text-center relative overflow-hidden">
<div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20"></div>
<div class="absolute bottom-0 left-0 w-48 h-48 bg-soft-mint/20 rounded-full -ml-10 -mb-10"></div>
<h2 class="text-white font-display text-4xl lg:text-5xl font-bold mb-6 relative z-10">Siap menemani perjalanan siklusmu?</h2>
<p class="text-baby-pink text-xl mb-10 max-w-2xl mx-auto relative z-10">
               Mulai kenali tubuhmu dengan lebih tenang, personal, dan penuh perhatian bersama Mirai.
            </p>
<div class="relative z-10">
<span class="inline-block bg-soft-mint text-primary font-extrabold px-6 py-2 rounded-full mb-8">COMING SOON</span>
<div class="flex flex-col sm:flex-row justify-center gap-4">
<button class="bg-white text-primary px-8 py-4 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-baby-pink transition-colors">
<span class="material-symbols-outlined">ios</span>
                        App Store
                    </button>
<button class="bg-white text-primary px-8 py-4 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-baby-pink transition-colors">
<span class="material-symbols-outlined">android</span>
                        Play Store
                    </button>
</div>
</div>
</div>
</div>
</section>

<!-- ===================== FAQ SECTION ===================== -->
<section class="py-24 bg-white dark:bg-gray-900" id="faq">
  <div class="max-w-4xl mx-auto px-4">

    <!-- Heading -->
    <div class="text-center mb-16">
      <h2 class="font-display text-4xl font-bold mb-4">
        Pertanyaan yang Sering Diajukan
      </h2>
      <p class="text-gray-600 dark:text-gray-400">
        Semua yang perlu kamu tahu tentang Mirai.
      </p>
    </div>

    <!-- FAQ LIST -->
    <div class="space-y-4">

      <!-- FAQ ITEM 1 -->
      <div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
        <button onclick="toggleFAQ(1)"
          class="w-full flex justify-between items-center p-6 text-left">
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

      <!-- FAQ ITEM 2 -->
      <div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
        <button onclick="toggleFAQ(2)"
          class="w-full flex justify-between items-center p-6 text-left">
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

      <!-- FAQ ITEM 3 -->
      <div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
        <button onclick="toggleFAQ(3)"
          class="w-full flex justify-between items-center p-6 text-left">
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

      <!-- FAQ ITEM 4 -->
      <div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
        <button onclick="toggleFAQ(4)"
          class="w-full flex justify-between items-center p-6 text-left">
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

      <!-- FAQ ITEM 5 -->
      <div class="border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden">
        <button onclick="toggleFAQ(5)"
          class="w-full flex justify-between items-center p-6 text-left">
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

<!-- ===================== FAQ SCRIPT ===================== -->
<script>
  function toggleFAQ(id) {
    const content = document.getElementById(`faq-${id}`);
    const icon = document.getElementById(`icon-${id}`);

    if (content.classList.contains("hidden")) {
      content.classList.remove("hidden");
      icon.textContent = "♥";
    } else {
      content.classList.add("hidden");
      icon.textContent = "♡";
    }
  }
</script>

    <!-- ===================== TEAM SECTION ===================== -->
<section class="py-24" id="team">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
        <h2 class="font-display text-4xl font-bold mb-4">Meet the Visionaries</h2>
        <p class="text-gray-600 dark:text-gray-400"> Tim pengembang di balik aplikasi prediksi siklus cerdas Mirai.</p>
</div>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
<div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/5 text-center group">
<div class="w-32 h-32 mx-auto mb-6 rounded-full overflow-hidden border-4 border-baby-pink">
<img
  src="img/videng.jpeg" alt="Videlma"class="w-full h-full object-cover group-hover:scale-110 transition-transform"/>
</div>
<h4 class="text-lg font-bold">Videlma</h4>
<p class="text-primary text-sm font-semibold mb-3">NIM: E3124 | UI/UX Designer & Machine Learning</p>
<p class="text-gray-500 text-sm">
Bertanggung jawab merancang tampilan antarmuka yang ramah pengguna serta berkontribusi dalam pengembangan model Machine Learning pada Mirai.
</p>
</div>
<div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/5 text-center group">
<div class="w-32 h-32 mx-auto mb-6 rounded-full overflow-hidden border-4 border-soft-mint">
<img alt="Elena Rodriguez" class="w-full h-full object-cover group-hover:scale-110 transition-transform" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDzeuiOIThMu0CoZsdziQav8HowQIYr0QXO_cECCfRPRqg0eAjPs8Ip-b82QVT-swi955KZ6C2xN3D44k04kTO4Yftnlv5HE9TRbxT-vXoxfKUkKYHubj2tymye2NS8lXZNoMr9G3mxTM-F7FS1Mp9_QgKAgzC3L4c2LbXCq9m1eEvoU7HIyzgsCbNNfKzGYfKC-6--NRkqa5Zyy5sfhfAJr215u6lVweneKsa5Du7T4gxuZWuGTTfH9oR6asIMw16G_Datnkj_pY1i"/>
</div>
<h4 class="text-lg font-bold">Adinda Riski</h4>
<p class="text-primary text-sm font-semibold mb-3">NIM: E3124 | Front End Web & Mobile</p>
<p class="text-gray-500 text-sm">
Mengembangkan tampilan web dan aplikasi mobile agar responsif, interaktif, dan sesuai dengan desain yang telah ditentukan.
</p>
</div>
<div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/5 text-center group">
<div class="w-32 h-32 mx-auto mb-6 rounded-full overflow-hidden border-4 border-peach-pink">
<img alt="David Kim" class="w-full h-full object-cover group-hover:scale-110 transition-transform" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBvwX6onLylow1un8iQOmt3XuDryL9mChfJAg59BWzlvf4fR7OdC4K6IqEdllnqrhKzcYDRsTjouu0u32d-4-YTukv1i883ffmhoKi0vUvaLersC6qIyuW-ugN26iDnJrFVLospo2P7a9u7zQE3MCBaAkjT27pTgICHK5l2eqEAYwOeVDTVz9_8EGYIN8rTZqxVub-KgCnuUV46AxuFVxK84q6gwivIRlTNCTTg-Rdi6NQWtveJpPUusi-zSoTnBCTLyLN-sJfDRROC"/>
</div>
<h4 class="text-lg font-bold">Risky Triana</h4>
<p class="text-primary text-sm font-semibold mb-3">NIM: E3124 | Back End & Machine Learning</p>
<p class="text-gray-500 text-sm">
Mengelola logika server, basis data, serta mendukung implementasi Machine Learning untuk memastikan sistem berjalan optimal.
</p>
</div>
<div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-white/5 text-center group">
<div class="w-32 h-32 mx-auto mb-6 rounded-full overflow-hidden border-4 border-baby-pink">
<img alt="Maya Patil" class="w-full h-full object-cover group-hover:scale-110 transition-transform" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAOP-z3tM9hvB-BNjBaQLb644NRYvsbtYsAKJ1JNskjVTGcdjB5DPVCmYVHTfe4aa3mr3ec_OnVtBtzUWW3d5YjM6IH5qbfSZf53LEYQH8k9bEM3fdKZo9SyDXdzlkhLebZU_UMjEJjVG9Ra5JrfbvtLtK-ozK9UpTn83N_K8Z-pxhoCdmpjITiUrgzw33RW0H4LpManXifT2_zKX5xEF3H0w6xoPrJE45SJDz_lKDLcbaE2hFYE5D_kXloKT3t53YYSZqB4LiEj-mt"/>
</div>
<h4 class="text-lg font-bold">Revina</h4>
<p class="text-primary text-sm font-semibold mb-3">NIM: E3124 | Asisten Front End</p>
<p class="text-gray-500 text-sm">
Mendukung pengembangan Front End dengan membantu implementasi komponen UI dan pengujian tampilan aplikasi.
</p>
</div>
</div>
</div>
</section>


    <!-- ===================== CONTACT SECTION ===================== -->
 <section class="py-24 bg-soft-mint/20 dark:bg-gray-900/50" id="contact">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid lg:grid-cols-2 gap-16">
<div>
<h2 class="font-display text-4xl font-bold mb-6">Let's Talk</h2>
<p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Have questions or suggestions? We'd love to hear from you. Our community is built on your feedback.
                </p>
<div class="space-y-6">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-primary shadow-sm">
<span class="material-symbols-outlined">mail</span>
</div>
<div>
<p class="text-sm text-gray-500">Email us at</p>
<p class="font-bold">hello@cyclepredictor.com</p>
</div>
</div>
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-primary shadow-sm">
<span class="material-symbols-outlined">chat</span>
</div>
<div>
<p class="text-sm text-gray-500">WhatsApp support</p>
<p class="font-bold">+62 82 2804 2840</p>
</div>
</div>
</div>
</div>
<div class="bg-white dark:bg-gray-800 p-8 lg:p-10 rounded-[2.5rem] shadow-xl border border-baby-pink/10">
<form class="space-y-6">
<div class="grid sm:grid-cols-2 gap-6">
<div>
<label class="block text-sm font-bold mb-2">Name</label>
<input class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary focus:bg-white transition-all" placeholder="Your name" type="text"/>
</div>
<div>
<label class="block text-sm font-bold mb-2">Email</label>
<input class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary focus:bg-white transition-all" placeholder="your@email.com" type="email"/>
</div>
</div>
<div>
<label class="block text-sm font-bold mb-2">Message</label>
<textarea class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary focus:bg-white transition-all" placeholder="How can we help?" rows="4"></textarea>
</div>
<button class="w-full bg-primary text-white font-bold py-4 rounded-2xl hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                        Send Message
                    </button>
</form>
</div>
</div>
</div>
</section>
    <!-- ===================== FOOTER ===================== -->
    <footer class="bg-baby-pink py-16 text-gray-800 dark:text-gray-800">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid md:grid-cols-4 gap-12 mb-12">
<div class="col-span-1 md:col-span-2">
<div class="flex items-center gap-3 mb-6">
<img
  alt="Logo"
  class="h-8 w-8 object-contain"
  src="img/logo fix.png" />
<span class="font-display font-bold text-xl tracking-tight text-primary">CyclePredictor</span>
</div>
<p class="text-gray-600 max-w-sm">
                    Empowering women through technology and data-driven insights. Our mission is to make health tracking simple, beautiful, and private.
                </p>
</div>
<div>
<h5 class="font-bold mb-6">Quick Links</h5>
<ul class="space-y-4 text-gray-600">
<li><a class="hover:text-primary transition-colors" href="#">Home</a></li>
<li><a class="hover:text-primary transition-colors" href="#about">About Us</a></li>
<li><a class="hover:text-primary transition-colors" href="#team">Our Team</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Privacy Policy</a></li>
</ul>
</div>
<div>
<h5 class="font-bold mb-6">Follow Us</h5>
<div class="flex gap-4">
<a class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary hover:scale-110 transition-all shadow-sm" href="#">
<span class="material-symbols-outlined text-xl">social_leaderboard</span>
</a>
<a class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary hover:scale-110 transition-all shadow-sm" href="#">
<span class="material-symbols-outlined text-xl">camera_alt</span>
</a>
<a class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary hover:scale-110 transition-all shadow-sm" href="#">
<span class="material-symbols-outlined text-xl">public</span>
</a>
</div>
</div>
</div>
<div class="pt-8 border-t border-primary/10 text-center text-gray-500 text-sm">
            © 2024 CyclePredictor. All rights reserved. Designed with love.
        </div>
</div>
</footer>

</body>

</html>
