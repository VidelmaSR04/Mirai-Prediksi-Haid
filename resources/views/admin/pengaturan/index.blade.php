@extends('admin.layout')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Admin & Sistem')
@section('search-placeholder', 'Cari pengaturan...')

@push('styles')
<style>
    .tab-btn { cursor:pointer; transition:all .2s; }
    .tab-btn.active { border-bottom-color:#E35D6A; color:#E35D6A; font-weight:700; }
    .tab-btn:not(.active) { border-bottom-color:transparent; color:#94a3b8; }
    .tab-panel { display:none; }
    .tab-panel.active { display:block; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-rose-100">
        <button class="tab-btn active pb-4 px-4 border-b-2 text-sm" onclick="switchTab(event,'profil')">Profil Admin</button>
        <button class="tab-btn pb-4 px-4 border-b-2 text-sm" onclick="switchTab(event,'keamanan')">Keamanan</button>
        <button class="tab-btn pb-4 px-4 border-b-2 text-sm" onclick="switchTab(event,'sistem')">Info Sistem</button>
    </div>

    {{-- TAB: PROFIL --}}
    <div id="tab-profil" class="tab-panel active">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 bg-white rounded-2xl border border-rose-100 shadow-sm p-7 space-y-7">
                <section>
                    <h3 class="text-base font-bold text-slate-800 mb-5">Informasi Personal</h3>
                    <form method="POST" action="{{ route('admin.pengaturan.update') }}">
                        @csrf @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                                    class="w-full border border-rose-100 bg-rose-50/20 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm p-3 outline-none transition-all @error('name') border-rose-500 @enderror"/>
                                @error('name')<p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Alamat Email</label>
                                <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                                    class="w-full border border-rose-100 bg-rose-50/20 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm p-3 outline-none transition-all @error('email') border-rose-500 @enderror"/>
                                @error('email')<p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Nomor Telepon</label>
                                <input type="text" name="telepon" value="{{ old('telepon', $admin->telepon ?? '') }}"
                                    placeholder="+62 812 xxxx xxxx"
                                    class="w-full border border-rose-100 bg-rose-50/20 rounded-xl focus:ring-2 focus:ring-primary/20 text-sm p-3 outline-none transition-all"/>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Role</label>
                                <input type="text" readonly value="{{ $admin->role ?? 'admin' }}"
                                    class="w-full border border-rose-50 bg-slate-50 rounded-xl text-slate-400 text-sm p-3 cursor-not-allowed"/>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="submit"
                                class="bg-primary text-white px-7 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-primary/20 hover:bg-primary/90 transition-all">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.pengaturan') }}"
                               class="bg-white text-slate-500 border border-slate-200 px-7 py-2.5 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                                Batalkan
                            </a>
                        </div>
                    </form>
                </section>

                <section class="pt-6 border-t border-rose-50">
                    <h3 class="text-base font-bold text-slate-800 mb-5">Preferensi Notifikasi</h3>
                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-4 bg-rose-50/30 border border-rose-100 rounded-xl cursor-pointer hover:bg-rose-50/60 transition-all">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">mail</span>
                                <span class="text-sm font-medium text-slate-700">Laporan Mingguan via Email</span>
                            </div>
                            <input type="checkbox" checked class="accent-primary w-4 h-4 rounded"/>
                        </label>
                        <label class="flex items-center justify-between p-4 bg-rose-50/30 border border-rose-100 rounded-xl cursor-pointer hover:bg-rose-50/60 transition-all">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">warning</span>
                                <span class="text-sm font-medium text-slate-700">Peringatan Kegagalan Sistem</span>
                            </div>
                            <input type="checkbox" checked class="accent-primary w-4 h-4 rounded"/>
                        </label>
                    </div>
                </section>
            </div>

            {{-- Avatar Card --}}
            <div class="bg-sidebar-pink/50 border border-rose-100 p-7 rounded-2xl text-center">
                <div class="w-24 h-24 rounded-2xl bg-primary/20 text-primary flex items-center justify-center text-3xl font-bold mx-auto mb-4 border-2 border-rose-200">
                    {{ strtoupper(substr($admin->name ?? 'A', 0, 1)) }}
                </div>
                <h4 class="text-lg font-bold text-slate-800">{{ $admin->name }}</h4>
                <p class="text-slate-400 text-xs mb-5">{{ $admin->email }}</p>
                <div class="space-y-0">
                    <div class="flex justify-between text-xs py-2.5 border-b border-rose-100">
                        <span class="text-slate-400 font-medium">Status Akun</span>
                        <span class="text-emerald-600 font-bold uppercase">Aktif</span>
                    </div>
                    <div class="flex justify-between text-xs py-2.5">
                        <span class="text-slate-400 font-medium">Role</span>
                        <span class="text-slate-600 font-bold capitalize">{{ $admin->role ?? 'admin' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB: KEAMANAN --}}
    <div id="tab-keamanan" class="tab-panel">
        <div class="max-w-xl">
            <div class="bg-white rounded-2xl border border-rose-100 shadow-sm p-7">
                <h3 class="text-base font-bold text-slate-800 mb-5">Ubah Password</h3>
                <form method="POST" action="{{ route('admin.pengaturan.password') }}">
                    @csrf @method('PATCH')
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Password Lama</label>
                            <input type="password" name="password_lama"
                                class="w-full border border-rose-100 bg-rose-50/20 rounded-xl focus:ring-2 focus:ring-primary/20 text-sm p-3 outline-none @error('password_lama') border-rose-500 @enderror"/>
                            @error('password_lama')<p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Password Baru</label>
                            <input type="password" name="password_baru"
                                class="w-full border border-rose-100 bg-rose-50/20 rounded-xl focus:ring-2 focus:ring-primary/20 text-sm p-3 outline-none @error('password_baru') border-rose-500 @enderror"/>
                            @error('password_baru')<p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru_confirmation"
                                class="w-full border border-rose-100 bg-rose-50/20 rounded-xl focus:ring-2 focus:ring-primary/20 text-sm p-3 outline-none"/>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit"
                            class="bg-primary text-white px-7 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-primary/20 hover:bg-primary/90 transition-all">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TAB: SISTEM --}}
    <div id="tab-sistem" class="tab-panel">
        <div class="max-w-xl">
            <div class="bg-white rounded-2xl border border-rose-100 shadow-sm p-7">
                <h3 class="text-base font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">info</span>
                    Informasi Sistem
                </h3>
                <div class="space-y-4">
                    @foreach([
                        'Versi Laravel'   => app()->version(),
                        'PHP'             => phpversion(),
                        'Database'        => 'MongoDB (' . env('MONGODB_DATABASE','mirai') . ')',
                        'Timezone Server' => config('app.timezone','Asia/Jakarta'),
                        'Lingkungan'      => app()->environment(),
                    ] as $label => $val)
                    <div class="flex justify-between items-center py-3 border-b border-rose-50">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $label }}</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function switchTab(event, name) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    event.currentTarget.classList.add('active');
}
</script>
@endpush
