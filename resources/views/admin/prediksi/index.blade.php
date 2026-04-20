@extends('admin.layout')
@section('title', 'Prediksi & Kesuburan')
@section('page-title', 'Prediksi & Kesuburan')
@section('search-placeholder', 'Cari data prediksi...')

@push('styles')
<style>
    .bar-fill{animation:growUp 1s cubic-bezier(.4,0,.2,1) forwards;transform-origin:bottom}
    @keyframes growUp{from{transform:scaleY(0)}to{transform:scaleY(1)}}
    .fade-in{opacity:0;animation:fadeIn .5s ease forwards}
    @keyframes fadeIn{to{opacity:1}}
    .delay-1{animation-delay:.1s}.delay-2{animation-delay:.2s}
    .delay-3{animation-delay:.3s}.delay-4{animation-delay:.4s}.delay-5{animation-delay:.5s}
    .phase-menstruasi{background:#FEE2E2;color:#E35D6A}
    .phase-folikel{background:#FEF3C7;color:#D97706}
    .phase-ovulasi{background:#D1FAE5;color:#059669}
    .phase-luteal{background:#EDE9FE;color:#7C3AED}
    .donut-ring{transition:stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1)}
</style>
@endpush

@section('content')

@if(isset($error))
<div class="mb-5 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-xl text-sm font-semibold">
    <span class="material-symbols-outlined text-primary">error</span>{{ $error }}
</div>
@endif

{{-- STAT CARDS — data dinamis dari controller --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="fade-in delay-1 bg-white rounded-2xl border border-rose-100 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-primary" style="font-size:22px">verified</span>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Akurasi Prediksi</p>
            <p class="text-2xl font-bold text-slate-800">{{ $avgConf ?? 0 }}<span class="text-sm text-slate-400">%</span></p>
            <p class="text-[10px] text-emerald-500 font-semibold mt-0.5">Confidence score rata-rata</p>
        </div>
    </div>
    <div class="fade-in delay-2 bg-white rounded-2xl border border-rose-100 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-amber-500" style="font-size:22px">query_stats</span>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Prediksi</p>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($totalPrediksi ?? 0) }}</p>
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">di database</p>
        </div>
    </div>
    <div class="fade-in delay-3 bg-white rounded-2xl border border-rose-100 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-blue-400" style="font-size:22px">update</span>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Rentang Normal</p>
            <p class="text-2xl font-bold text-slate-800">21–35<span class="text-sm text-slate-400"> hari</span></p>
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Siklus menstruasi normal</p>
        </div>
    </div>
    <div class="fade-in delay-4 bg-white rounded-2xl border border-rose-100 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-emerald-500" style="font-size:22px">favorite</span>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Fase Ovulasi</p>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($sedangSubur ?? 0) }}</p>
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">pengguna saat ini</p>
        </div>
    </div>
</div>

{{-- CHARTS --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Bar Chart Distribusi Fase --}}
    <div class="fade-in delay-2 lg:col-span-2 bg-white rounded-2xl border border-rose-100 shadow-sm p-7">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-800">Distribusi Fase Siklus</h3>
                <p class="text-xs text-slate-400 mt-0.5">Volume prediksi per fase saat ini</p>
            </div>
        </div>
        @php
            $faseData = [
                ['Folikel',    $faseCount['folikel']    ?? 0, 'amber'],
                ['Ovulasi',    $faseCount['ovulasi']    ?? 0, 'emerald'],
                ['Luteal',     $faseCount['luteal']     ?? 0, 'violet'],
                ['Menstruasi', $faseCount['menstruasi'] ?? 0, 'rose'],
            ];
            $maxFase = max(array_column($faseData, 1) ?: [1]);
        @endphp
        <div class="flex items-end gap-4 h-44 px-2">
            @foreach($faseData as [$label, $val, $color])
            @php $pct = $maxFase > 0 ? round($val / $maxFase * 100) : 0; @endphp
            <div class="flex-1 flex flex-col items-center gap-2">
                <span class="text-xs font-bold text-{{ $color }}-600">{{ $val }}</span>
                <div class="w-full relative bg-{{ $color }}-50 rounded-xl overflow-hidden" style="height:120px">
                    <div class="bar-fill absolute bottom-0 w-full bg-gradient-to-t from-{{ $color }}-500 to-{{ $color }}-400 rounded-xl"
                         style="height:{{ $pct }}%"></div>
                </div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ $label }}</span>
            </div>
            @endforeach
        </div>
        <div class="mt-5 pt-5 border-t border-rose-50 flex items-center justify-between">
            <p class="text-xs text-slate-400">Total prediksi tersimpan</p>
            <p class="text-sm font-bold text-slate-800">{{ number_format($totalPrediksi ?? 0) }} <span class="text-slate-400 font-normal">data</span></p>
        </div>
    </div>

    {{-- Donut Chart Confidence --}}
    <div class="fade-in delay-3 bg-white rounded-2xl border border-rose-100 shadow-sm p-7 flex flex-col">
        <div class="mb-5">
            <h3 class="text-base font-bold text-slate-800">Confidence Score</h3>
            <p class="text-xs text-slate-400 mt-0.5">Rata-rata akurasi prediksi model</p>
        </div>
        <div class="flex flex-col items-center flex-1 justify-center">
            @php
                $conf    = (float)($avgConf ?? 0);
                $circ    = 238.76;
                $offset  = $circ - ($conf / 100 * $circ);
            @endphp
            <div class="relative w-36 h-36">
                <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                    <circle cx="50" cy="50" r="38" fill="none" stroke="#FFF0F1" stroke-width="10"/>
                    <circle cx="50" cy="50" r="38" fill="none" stroke="#E35D6A" stroke-width="10"
                        stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $offset }}"
                        stroke-linecap="round" class="donut-ring"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-slate-800">{{ $conf }}%</span>
                    <span class="text-[10px] text-slate-400 font-semibold">Confidence</span>
                </div>
            </div>
        </div>
        <div class="mt-5 space-y-2.5">
            @php
                $normalPred = collect($prediksi ?? [])->where('cycle_status', 'normal')->count();
                $abnormal   = ($totalPrediksi ?? 0) - $normalPred;
            @endphp
            <div class="flex items-center justify-between bg-rose-50/60 rounded-xl px-4 py-2.5">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                    <span class="text-xs font-semibold text-slate-600">Siklus Normal</span>
                </div>
                <span class="text-xs font-bold text-slate-800">{{ $normalPred }}</span>
            </div>
            <div class="flex items-center justify-between bg-slate-50 rounded-xl px-4 py-2.5">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                    <span class="text-xs font-semibold text-slate-600">Tidak Normal</span>
                </div>
                <span class="text-xs font-bold text-slate-800">{{ $abnormal }}</span>
            </div>
        </div>
    </div>
</div>

{{-- TABLE Prediksi per Pengguna --}}
<div class="fade-in delay-5 bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-rose-50">
        <h3 class="text-base font-bold text-slate-800">Data Prediksi Per Pengguna</h3>
        <p class="text-xs text-slate-400 mt-0.5">Pantau status siklus dan prediksi kesuburan individu</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-rose-50/30 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-rose-50">
                <tr>
                    <th class="px-6 py-4">Pengguna</th>
                    <th class="px-6 py-4 text-center">Usia</th>
                    <th class="px-6 py-4 text-center">Panjang Siklus</th>
                    <th class="px-6 py-4">Fase Saat Ini</th>
                    <th class="px-6 py-4">Est. Ovulasi</th>
                    <th class="px-6 py-4">Prediksi Haid</th>
                    <th class="px-6 py-4 text-center">Confidence</th>
                    <th class="px-6 py-4 text-center">MAE</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50/70">
                @forelse($prediksi ?? [] as $p)
                @php $fase = strtolower($p['pattern'] ?? 'folikel'); @endphp
                <tr class="hover:bg-rose-50/20 transition-colors">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-rose-100 text-primary flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($p['nama'] ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $p['nama'] ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">ID: {{ $p['id_user'] ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-center text-sm font-semibold text-slate-600">
                        {{ $p['usia'] ?? '-' }} <span class="text-slate-400">thn</span>
                    </td>
                    <td class="px-6 py-3.5 text-center text-sm font-semibold text-slate-600">
                        {{ $p['panjang_siklus'] ?? '-' }} <span class="text-slate-400">hari</span>
                    </td>
                    <td class="px-6 py-3.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider phase-{{ $fase }}">
                            {{ ucfirst($fase) }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-sm text-slate-600">{{ $p['ovulasi'] ?? '-' }}</td>
                    <td class="px-6 py-3.5 text-sm font-semibold text-slate-700">{{ $p['tanggal_mulai_haid'] ?? '-' }}</td>
                    <td class="px-6 py-3.5 text-center">
                        <span class="text-xs font-bold {{ (($p['confidence_score'] ?? 0) >= 80) ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $p['confidence_score'] ?? '-' }}%
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-center">
                        <span class="text-xs font-bold text-primary">{{ $p['mae_error'] ?? '-' }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                        <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">search_off</span>
                        Belum ada data prediksi — import predictions.json ke MongoDB dulu
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
