@extends('admin.layout')
@section('title', 'Dashboard')
@section('page-title', 'Ringkasan Dashboard')
@section('search-placeholder', 'Cari log sistem...')

@push('styles')
<style>
.fade-in{opacity:0;animation:fadeIn .45s ease forwards}
@keyframes fadeIn{to{opacity:1}}
.d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}
.d4{animation-delay:.2s}.d5{animation-delay:.25s}.d6{animation-delay:.3s}
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

{{-- STAT CARDS - Layout baru: 3 + 3 --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Baris 1 -->
    <div class="fade-in d1 bg-white rounded-3xl border border-rose-100 p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-rose-500 text-3xl">update</span>
            </div>
            <div>
                <p class="text-4xl font-bold text-slate-800">{{ $stats['rata_siklus'] }}</p>
                <p class="text-sm font-semibold text-slate-500">RATA-RATA SIKLUS</p>
                <p class="text-xs text-slate-400 mt-1">hari</p>
            </div>
        </div>
    </div>

    <div class="fade-in d2 bg-white rounded-3xl border border-rose-100 p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-emerald-500 text-3xl">verified</span>
            </div>
            <div>
                <p class="text-4xl font-bold text-slate-800">{{ $stats['persen_normal'] }}%</p>
                <p class="text-sm font-semibold text-slate-500">SIKLUS NORMAL</p>
                <p class="text-xs text-slate-400 mt-1">21–35 hari</p>
            </div>
        </div>
    </div>

    <div class="fade-in d3 bg-white rounded-3xl border border-rose-100 p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-blue-500 text-3xl">history_edu</span>
            </div>
            <div>
                <p class="text-4xl font-bold text-slate-800">{{ number_format($stats['total_siklus']) }}</p>
                <p class="text-sm font-semibold text-slate-500">TOTAL DATA SIKLUS</p>
            </div>
        </div>
    </div>

    <!-- Baris 2 -->
    <div class="fade-in d4 bg-white rounded-3xl border border-rose-100 p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-amber-500 text-3xl">query_stats</span>
            </div>
            <div>
                <p class="text-4xl font-bold text-slate-800">{{ $stats['mae'] }}</p>
                <p class="text-sm font-semibold text-slate-500">MAE MODEL</p>
                <p class="text-xs text-slate-400 mt-1">Mean Absolute Error (hari)</p>
            </div>
        </div>
    </div>

    <div class="fade-in d5 bg-white rounded-3xl border border-rose-100 p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-violet-500 text-3xl">analytics</span>
            </div>
            <div>
                <p class="text-4xl font-bold text-slate-800">{{ $stats['rmse'] }}</p>
                <p class="text-sm font-semibold text-slate-500">RMSE MODEL</p>
                <p class="text-xs text-slate-400 mt-1">Root Mean Square Error (hari)</p>
            </div>
        </div>
    </div>

    <div class="fade-in d6 bg-white rounded-3xl border border-rose-100 p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-teal-500 text-3xl">model_training</span>
            </div>
            <div>
                <p class="text-4xl font-bold text-slate-800">{{ $stats['r2'] }}</p>
                <p class="text-sm font-semibold text-slate-500">R² SCORE</p>
                <p class="text-xs text-slate-400 mt-1">0 – 1 (semakin mendekati 1 semakin baik)</p>
            </div>
        </div>
    </div>

</div>

@endsection
