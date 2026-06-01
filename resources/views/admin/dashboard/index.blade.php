@extends('admin.layout')
@section('title', 'Dashboard')
@section('page-title', 'Ringkasan Dashboard')
@section('search-placeholder', 'Cari log sistem...')

@push('styles')
<style>
.fade-in{
    opacity:0;
    animation:fadeInScale 0.6s ease forwards;
}
@keyframes fadeInScale{
    from {
        opacity:0;
        transform: scale(0.88) translateY(25px);
    }
    to {
        opacity:1;
        transform: scale(1) translateY(0);
    }
}

.d1{animation-delay:.05s}
.d2{animation-delay:.1s}
.d3{animation-delay:.15s}
.d4{animation-delay:.25s}
.d5{animation-delay:.3s}
.d6{animation-delay:.35s}

/* PERBESARAN KOTAK + LEBAR MAKSIMAL */
.stat-card {
    padding: 36px 32px !important;
    min-height: 190px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover {
    transform: translateY(-10px) scale(1.04);
    box-shadow: 0 30px 60px -15px rgb(0 0 0 / 0.15);
}

.stat-number {
    font-size: 3.8rem !important;
    line-height: 1;
    font-weight: 700;
    margin-bottom: 6px;
}

.stat-label {
    font-size: 1.25rem !important;
    font-weight: 600;
    color: #1e2937;
}

.stat-subtext {
    font-size: 1rem;
    color: #64748b;
}

/* Membuat grid lebih lebar dan penuh */
.grid-container {
    max-width: 100% !important;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}
</style>
@endpush

@section('content')

@php
$stats = $stats ?? [
    'rata_siklus'   => 0,
    'persen_normal' => 0,
    'total_siklus'  => 0,
    'mae'           => 0,
    'rmse'          => 0,
    'r2'            => 0,
];
@endphp

@if(isset($error))
<div class="mb-6 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-2xl text-sm">
    <span class="material-symbols-outlined">error</span> {{ $error }}
</div>
@endif

<div class="grid-container">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">  {{-- gap lebih kecil agar lebih lebar --}}

        <!-- Baris 1 -->
        <div class="fade-in d1 stat-card bg-white rounded-3xl border border-rose-100 shadow-sm">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-rose-500 text-5xl">update</span>
                </div>
                <div>
                    <p class="stat-number text-slate-800">{{ $stats['rata_siklus'] }}</p>
                    <p class="stat-label">RATA-RATA SIKLUS</p>
                    <p class="stat-subtext">hari</p>
                </div>
            </div>
        </div>

        <div class="fade-in d2 stat-card bg-white rounded-3xl border border-rose-100 shadow-sm">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-emerald-500 text-5xl">verified</span>
                </div>
                <div>
                    <p class="stat-number text-slate-800">{{ $stats['persen_normal'] }}%</p>
                    <p class="stat-label">SIKLUS NORMAL</p>
                    <p class="stat-subtext">21–35 hari</p>
                </div>
            </div>
        </div>

        <div class="fade-in d3 stat-card bg-white rounded-3xl border border-rose-100 shadow-sm">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-blue-500 text-5xl">history_edu</span>
                </div>
                <div>
                    <p class="stat-number text-slate-800">{{ number_format($stats['total_siklus']) }}</p>
                    <p class="stat-label">TOTAL DATA SIKLUS</p>
                </div>
            </div>
        </div>

        <!-- Baris 2 -->
        <div class="fade-in d4 stat-card bg-white rounded-3xl border border-rose-100 shadow-sm">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-amber-500 text-5xl">query_stats</span>
                </div>
                <div>
                    <p class="stat-number text-slate-800">{{ $stats['mae'] }}</p>
                    <p class="stat-label">MAE MODEL</p>
                    <p class="stat-subtext">Mean Absolute Error (hari)</p>
                </div>
            </div>
        </div>

        <div class="fade-in d5 stat-card bg-white rounded-3xl border border-rose-100 shadow-sm">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-violet-500 text-5xl">analytics</span>
                </div>
                <div>
                    <p class="stat-number text-slate-800">{{ $stats['rmse'] }}</p>
                    <p class="stat-label">RMSE MODEL</p>
                    <p class="stat-subtext">Root Mean Square Error (hari)</p>
                </div>
            </div>
        </div>

        <div class="fade-in d6 stat-card bg-white rounded-3xl border border-rose-100 shadow-sm">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-teal-50 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-teal-500 text-5xl">model_training</span>
                </div>
                <div>
                    <p class="stat-number text-slate-800">{{ $stats['r2'] }}</p>
                    <p class="stat-label">R² SCORE</p>
                    <p class="stat-subtext">0 – 1 (semakin mendekati 1 semakin baik)</p>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
