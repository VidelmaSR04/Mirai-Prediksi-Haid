@extends('admin.layout')
@section('title', 'Laporan')
@section('page-title', 'Pusat Laporan & Ekspor')
@section('search-placeholder', 'Cari laporan...')

@push('styles')
<style>
    .tpl-card{cursor:pointer;transition:all .2s}
    .tpl-card:hover{border-color:#E35D6A;box-shadow:0 0 0 3px rgba(227,93,106,.08)}
    .tpl-card.active{border-color:#E35D6A;box-shadow:0 0 0 3px rgba(227,93,106,.15);background:#FDF2F3}
    #toast{transition:all .3s;transform:translateY(20px);opacity:0;pointer-events:none}
    #toast.show{transform:translateY(0);opacity:1}
    .fade-in{opacity:0;animation:fi .45s ease forwards}
    @keyframes fi{to{opacity:1}}
    .d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}
    .d4{animation-delay:.2s}.d5{animation-delay:.3s}.d6{animation-delay:.4s}
</style>
@endpush

@section('content')

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="fade-in d1 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-primary" style="font-size:20px">description</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] ?? 48 }}</p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Total Laporan</p>
    </div>
    <div class="fade-in d2 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-emerald-500" style="font-size:20px">download_done</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">5</p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Ekspor Hari Ini</p>
    </div>
    <div class="fade-in d3 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-amber-500" style="font-size:20px">schedule</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">2</p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Terjadwal Otomatis</p>
    </div>
    <div class="fade-in d4 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-blue-400" style="font-size:20px">storage</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">128<span class="text-base text-slate-400"> MB</span></p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Total Ukuran Arsip</p>
    </div>
</div>

{{-- TEMPLATE + KONFIGURASI --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="fade-in d4 bg-white rounded-2xl border border-rose-100 shadow-sm p-7">
        <h3 class="text-base font-bold text-slate-800 mb-1">Template Laporan</h3>
        <p class="text-xs text-slate-400 mb-5">Pilih jenis laporan yang ingin dibuat</p>
        <div class="space-y-3" id="templateList">
            @foreach([
                ['Laporan Bulanan Sistem','calendar_month','bg-rose-100 text-primary','Ringkasan aktivitas & pengguna'],
                ['Data Prediksi & Kesuburan','favorite','bg-emerald-50 text-emerald-500','Analisis siklus & akurasi model'],
                ['Demografis Pengguna','group','bg-violet-50 text-violet-500','Usia, lokasi, & retensi'],
                ['Performa Algoritma','model_training','bg-amber-50 text-amber-500','Akurasi & validasi model AI'],
            ] as [$label,$icon,$cls,$desc])
            <div class="tpl-card {{ $loop->first ? 'active' : 'border-2 border-slate-100' }} rounded-xl p-4 flex items-center gap-3"
                 onclick="selectTemplate(this,'{{ $label }}')">
                <div class="w-10 h-10 rounded-xl {{ $cls }} flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined" style="font-size:18px">{{ $icon }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800">{{ $label }}</p>
                    <p class="text-[10px] text-slate-400">{{ $desc }}</p>
                </div>
                <span class="material-symbols-outlined text-lg check-icon" style="color:{{ $loop->first ? '#E35D6A' : '#E2E8F0' }}">check_circle</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="fade-in d5 lg:col-span-2 bg-white rounded-2xl border border-rose-100 shadow-sm p-7 flex flex-col">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="text-base font-bold text-slate-800">Konfigurasi Laporan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Template: <span id="selectedTplLabel" class="font-semibold text-primary">Laporan Bulanan Sistem</span></p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.laporan.generate') }}" class="flex-1 flex flex-col">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 flex-1">
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Rentang Waktu</label>
                    <div class="flex gap-2">
                        <input type="date" name="dari" class="w-full px-3 py-2.5 bg-slate-50 border border-rose-100 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 outline-none"/>
                        <input type="date" name="sampai" class="w-full px-3 py-2.5 bg-slate-50 border border-rose-100 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 outline-none"/>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Format Ekspor</label>
                    <div class="flex gap-2">
                        <button type="button" onclick="setFormat('PDF')" id="fmtPDF"
                            class="fmt-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-primary bg-rose-50 text-primary text-xs font-bold">
                            <span class="material-symbols-outlined" style="font-size:16px">picture_as_pdf</span> PDF
                        </button>
                        <button type="button" onclick="setFormat('XLSX')" id="fmtXLSX"
                            class="fmt-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-slate-100 text-slate-500 text-xs font-bold">
                            <span class="material-symbols-outlined" style="font-size:16px">table_view</span> Excel
                        </button>
                        <button type="button" onclick="setFormat('CSV')" id="fmtCSV"
                            class="fmt-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-slate-100 text-slate-500 text-xs font-bold">
                            <span class="material-symbols-outlined" style="font-size:16px">csv</span> CSV
                        </button>
                    </div>
                    <input type="hidden" name="format" id="formatInput" value="PDF"/>
                    <input type="hidden" name="template" id="templateInput" value="Laporan Bulanan Sistem"/>
                </div>
            </div>

            <div class="mt-5 pt-5 border-t border-rose-50 flex items-center gap-3">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary/90 transition-all shadow-md shadow-primary/20">
                    <span class="material-symbols-outlined text-lg">download</span>
                    Buat & Ekspor Laporan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- RIWAYAT EKSPOR --}}
<div class="fade-in d6 bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-rose-50 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-800">Riwayat Ekspor Laporan</h3>
            <p class="text-xs text-slate-400 mt-0.5">Semua laporan yang telah dibuat</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-rose-50/30 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-rose-50">
                <tr>
                    <th class="px-7 py-4">Nama Laporan</th>
                    <th class="px-5 py-4 text-center">Format</th>
                    <th class="px-5 py-4">Dibuat Oleh</th>
                    <th class="px-5 py-4">Waktu</th>
                    <th class="px-5 py-4 text-center">Status</th>
                    <th class="px-7 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
                @forelse($laporan ?? [] as $l)
                <tr class="hover:bg-rose-50/10 transition-colors">
                    <td class="px-7 py-4 text-sm font-semibold text-slate-800">{{ $l['nama'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2.5 py-1 text-[9px] font-bold uppercase rounded-full border bg-red-50 text-red-500 border-red-100">{{ $l['format'] ?? '-' }}</span>
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-600">{{ $l['oleh'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-xs text-slate-400">{{ $l['waktu'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-bold rounded-full border border-emerald-100">Selesai</span>
                    </td>
                    <td class="px-7 py-4 text-right">
                        <form method="POST" action="{{ route('admin.laporan.destroy', $l['id']) }}" class="inline">
                            @csrf @method('DELETE')
                            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-primary transition-colors ml-auto">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-7 py-12 text-center text-slate-400">
                        <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">description</span>
                        Belum ada laporan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function selectTemplate(el, label) {
    document.querySelectorAll(".tpl-card").forEach(c => {
        c.classList.remove("active");
        c.querySelector(".check-icon").style.color = "#E2E8F0";
    });
    el.classList.add("active");
    el.querySelector(".check-icon").style.color = "#E35D6A";
    document.getElementById("selectedTplLabel").textContent = label;
    document.getElementById("templateInput").value = label;
}
let selectedFormat = "PDF";
function setFormat(fmt) {
    selectedFormat = fmt;
    document.querySelectorAll(".fmt-btn").forEach(b => {
        b.classList.remove("border-primary","bg-rose-50","text-primary");
        b.classList.add("border-slate-100","text-slate-500");
    });
    const btn = document.getElementById("fmt" + fmt);
    btn.classList.add("border-primary","bg-rose-50","text-primary");
    btn.classList.remove("border-slate-100","text-slate-500");
    document.getElementById("formatInput").value = fmt;
}
</script>
@endpush
