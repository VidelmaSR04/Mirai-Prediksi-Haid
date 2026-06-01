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

    {{-- Flash Message --}}
    @if(session('error'))
    <div class="mb-4 px-5 py-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">
        {{ session('error') }}
    </div>
    @endif
    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-sm">
        {{ session('success') }}
    </div>
    @endif

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

        {{-- TEMPLATE LAPORAN (3 Template) --}}
        <div class="lg:col-span-5 bg-white rounded-3xl border border-rose-100 p-7">
            <h3 class="font-bold text-lg mb-2">Template Laporan</h3>
            <p class="text-slate-500 mb-6 text-sm">Pilih jenis laporan yang ingin diunduh</p>

            <div class="space-y-3" id="templateList">

                {{-- Template 1: Profil & Demografi --}}
                <div class="tpl-card active flex items-center gap-4 p-5 rounded-2xl border-2 border-rose-300"
                     onclick="selectTemplate(this, 'Profil & Demografi Pengguna')">
                    <div class="w-11 h-11 bg-violet-50 rounded-2xl flex items-center justify-center text-violet-500 shrink-0">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-800">Profil & Demografi Pengguna</p>
                        <p class="text-xs text-slate-500 mt-0.5">Nama, email, usia, BMI, domisili & status akun</p>
                    </div>
                </div>

                {{-- Template 2: Riwayat Siklus --}}
                <div class="tpl-card flex items-center gap-4 p-5 rounded-2xl border-2 border-slate-100"
                     onclick="selectTemplate(this, 'Riwayat Siklus Menstruasi')">
                    <div class="w-11 h-11 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 shrink-0">
                        <span class="material-symbols-outlined">calendar_month</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-800">Riwayat Siklus Menstruasi</p>
                        <p class="text-xs text-slate-500 mt-0.5">Data siklus, gejala, hormon & rata-rata per pengguna</p>
                    </div>
                </div>

                {{-- Template 3: Ringkasan Prediksi AI --}}
                <div class="tpl-card flex items-center gap-4 p-5 rounded-2xl border-2 border-slate-100"
                     onclick="selectTemplate(this, 'Ringkasan Prediksi AI')">
                    <div class="w-11 h-11 bg-sky-50 rounded-2xl flex items-center justify-center text-sky-500 shrink-0">
                        <span class="material-symbols-outlined">neurology</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-800">Ringkasan Prediksi AI</p>
                        <p class="text-xs text-slate-500 mt-0.5">Hasil prediksi, tingkat risiko & rekomendasi sistem</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- KONFIGURASI --}}
        <div class="lg:col-span-7 bg-white rounded-3xl border border-rose-100 p-7">
            <h3 class="font-bold text-lg mb-2">Konfigurasi Laporan</h3>
            <p class="text-sm text-slate-500 mb-6">
                Template dipilih: <span id="labelTemplate" class="font-semibold text-rose-500">Profil & Demografi Pengguna</span>
            </p>

            <form method="POST" action="{{ route('admin.laporan.generate') }}">
                @csrf
                <input type="hidden" name="format" value="CSV">
                <input type="hidden" name="template" id="templateInput" value="Profil & Demografi Pengguna">

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-600 mb-2">Rentang Waktu</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-400 mb-1">Dari</p>
                            <input type="date" name="dari" value="{{ now()->subDays(30)->format('Y-m-d') }}"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm">
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-1">Sampai</p>
                            <input type="date" name="sampai" value="{{ now()->format('Y-m-d') }}"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm">
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">
                        * Rentang waktu berlaku untuk template Siklus & Prediksi. Template Demografi mengambil semua data pengguna.
                    </p>
                </div>

                <button type="submit"
                        class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-4 rounded-2xl flex items-center justify-center gap-3 transition">
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
                    <tr class="text-xs uppercase text-slate-500">
                        <th class="px-6 py-4 text-left">Nama Laporan</th>
                        <th class="px-6 py-4 text-center">Format</th>
                        <th class="px-6 py-4 text-left">Dibuat Oleh</th>
                        <th class="px-6 py-4 text-left">Waktu</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($laporan ?? [] as $l)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ $l['nama'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs bg-emerald-50 text-emerald-700 rounded-lg">CSV</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $l['oleh'] }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $l['waktu'] }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.laporan.destroy', $l['id']) }}" class="inline"
                                  onsubmit="return confirm('Hapus laporan ini?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:text-red-700 px-3 py-1 rounded-lg hover:bg-red-50 transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">Belum ada riwayat ekspor laporan</td>
                    </tr>
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
        card.classList.add('border-slate-100');
        card.classList.remove('border-rose-300');
    });
    el.classList.add('active');
    el.classList.remove('border-slate-100');
    el.classList.add('border-rose-300');
    document.getElementById('templateInput').value = label;
    document.getElementById('labelTemplate').textContent = label;
}

document.addEventListener('DOMContentLoaded', function () {
    const first = document.querySelector('.tpl-card');
    if (first) first.classList.add('active');
});
</script>
@endpush
