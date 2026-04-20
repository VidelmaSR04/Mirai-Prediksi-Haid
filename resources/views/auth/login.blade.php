<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MIRAI Admin — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-rose-50 flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-rose-500 rounded-2xl shadow-lg mb-4">
                <span class="material-symbols-outlined text-white text-3xl">auto_awesome</span>
            </div>
            <h1 class="text-2xl font-bold text-rose-500">MIRAI</h1>
            <p class="text-slate-500 text-sm mt-1">Admin Panel</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-rose-100 p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-1">Selamat Datang</h2>
            <p class="text-slate-400 text-sm mb-6">Masuk ke dashboard administrator</p>

            {{-- Error --}}
            @if($errors->any())
                <div class="mb-4 p-4 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400
                                  @error('email') border-rose-400 @enderror"
                           placeholder="admin@mirai.com" required autofocus/>
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400"
                           placeholder="••••••••" required/>
                </div>

                {{-- Remember --}}
                <div class="flex items-center mb-6">
                    <input type="checkbox" name="remember" id="remember"
                           class="rounded border-gray-300 text-rose-500 focus:ring-rose-300"/>
                    <label for="remember" class="ml-2 text-sm text-slate-600">Ingat saya</label>
                </div>

                <button type="submit"
                        class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold
                               py-2.5 rounded-xl transition-colors text-sm">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            © {{ date('Y') }} MIRAI — Sistem Admin
        </p>
    </div>

</body>
</html>
