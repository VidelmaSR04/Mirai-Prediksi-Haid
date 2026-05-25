@extends('admin.layout')
@section('title', 'Laporan')
@section('page-title', 'Pusat Laporan & Ekspor')

@push('styles')
<style>
    .tpl-card {
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .tpl-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px -5px rgba(227, 93, 106, 0.15);
    }
    .tpl-card.active {
        border-color: #E35D6A !important;
        background: #FDF2F3;
        box-shadow: 0 0 0 4px rgba(227,93,106,0.15);
    }
</style>
@endpush

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-rose-100 p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-rose-50 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-rose-500">description</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['total'] ?? 0 }}</p>
                    <p class="text-xs text-slate-500">TOTAL LAPORAN</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-rose-100 p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-emerald-50 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-500">download_done</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-slate-800">{{ $stats['ekspor_hari_ini'] ?? 0 }}</p>
                    <p class="text-xs text-slate-500">EKSPOR HARI INI</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- TEMPLATE LAPORAN (Hanya 2) --}}
        <div class="lg:col-span-5 bg-white rounded-3xl border border-rose-100 p-7">
            <h3 class="font-bold text-lg mb-2">Template Laporan</h3>
            <p class="text-slate-500 mb-6">Pilih jenis laporan yang ingin diunduh</p>

            <div class="space-y-3" id="templateList">
                <!-- Template 1 -->
                <div class="tpl-card flex items-center gap-4 p-5 rounded-2xl border-2 border-rose-300"
                     onclick="selectTemplate(this, 'Demografis Pengguna')">
                    <div class="w-11 h-11 bg-violet-50 rounded-2xl flex items-center justify-center text-violet-500">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-800">Demografis Pengguna</p>
                        <p class="text-sm text-slate-500">Usia, BMI, lokasi, & profil pengguna</p>
                    </div>
                </div>

                <!-- Template 2 -->
                <div class="tpl-card flex items-center gap-4 p-5 rounded-2xl border-2 border-slate-100"
                     onclick="selectTemplate(this, 'Log Siklus Menstruasi')">
                    <div class="w-11 h-11 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500">
                        <span class="material-symbols-outlined">calendar_month</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-800">Log Siklus Menstruasi</p>
                        <p class="text-sm text-slate-500">Data siklus, gejala, & hormon</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- KONFIGURASI --}}
        <div class="lg:col-span-7 bg-white rounded-3xl border border-rose-100 p-7">
            <h3 class="font-bold text-lg mb-6">Konfigurasi Laporan</h3>

            <form method="POST" action="{{ route('admin.laporan.generate') }}">
                @csrf
                <input type="hidden" name="format" value="CSV">
                <input type="hidden" name="template" id="templateInput" value="Demografis Pengguna">

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-600 mb-2">Rentang Waktu</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="date" name="dari" value="{{ now()->subDays(30)->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-slate-200 rounded-2xl">
                        <input type="date" name="sampai" value="{{ now()->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-slate-200 rounded-2xl">
                    </div>
                </div>

                <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-4 rounded-2xl flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined">download</span>
                    Buat & Unduh Laporan CSV
                </button>
            </form>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <div class="mt-10 bg-white rounded-3xl border border-rose-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="font-bold text-lg">Riwayat Ekspor Laporan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-rose-50">
                    <tr>
                        <th class="px-6 py-4 text-left">Nama Laporan</th>
                        <th class="px-6 py-4 text-center">Format</th>
                        <th class="px-6 py-4">Dibuat Oleh</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($laporan ?? [] as $l)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">{{ $l['nama'] }}</td>
                        <td class="px-6 py-4 text-center">CSV</td>
                        <td class="px-6 py-4">{{ $l['oleh'] }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $l['waktu'] }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.laporan.destroy', $l['id']) }}" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-12 text-center text-slate-400">Belum ada laporan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function selectTemplate(el, label) {
    document.querySelectorAll('.tpl-card').forEach(card => {
        card.classList.remove('active');
    });
    el.classList.add('active');
    document.getElementById('templateInput').value = label;
}

// Default pertama kali
document.addEventListener('DOMContentLoaded', function() {
    const first = document.querySelector('.tpl-card');
    if (first) selectTemplate(first, 'Demografis Pengguna');
});
</script>
@endpush
