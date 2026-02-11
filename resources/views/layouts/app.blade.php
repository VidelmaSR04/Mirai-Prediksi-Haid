<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mirai | Index')</title>

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

    @stack('styles')
</head>

<body class="font-sans bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-100 transition-colors duration-300">

    <!-- Include Navbar -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Include Footer -->
    @include('layouts.partials.footer')

    <!-- Scripts -->
    @stack('scripts')

</body>

</html>