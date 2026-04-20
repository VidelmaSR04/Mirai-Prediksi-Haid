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
$stats = array_merge([
    'rata_siklus' => 0,
    'persen_normal' => 0,
    'total_siklus' => 0,
    'mae' => 0,
    'rmse' => 0,
    'r2' => 0,
], $stats ?? []);
@endphp

@if(isset($error))
<div class="mb-5 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-xl text-sm font-semibold">
    <span class="material-symbols-outlined text-primary">error</span> {{ $error }}
</div>
@endif

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">

    <div class="fade-in d1 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:-translate-y-0.5 transition-all">
        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-primary" style="font-size:20px">update</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['rata_siklus'] }}</p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Rata-rata Siklus</p>
        <p class="text-[10px] text-slate-400 mt-1">hari</p>
    </div>

    <div class="fade-in d2 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:-translate-y-0.5 transition-all">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-emerald-500" style="font-size:20px">verified</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['persen_normal'] }}%</p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Siklus Normal</p>
        <p class="text-[10px] text-slate-400 mt-1">21–35 hari</p>
    </div>

    <div class="fade-in d3 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:-translate-y-0.5 transition-all">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-blue-400" style="font-size:20px">history_edu</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_siklus']) }}</p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">Total Data Siklus</p>
    </div>

    <div class="fade-in d4 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:-translate-y-0.5 transition-all">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-amber-500" style="font-size:20px">query_stats</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['mae'] }}</p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">MAE Model</p>
        <p class="text-[10px] text-slate-400 mt-1">hari</p>
    </div>

    <div class="fade-in d5 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:-translate-y-0.5 transition-all">
        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-violet-500" style="font-size:20px">analytics</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['rmse'] }}</p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">RMSE Model</p>
        <p class="text-[10px] text-slate-400 mt-1">hari</p>
    </div>

    <div class="fade-in d6 bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:-translate-y-0.5 transition-all">
        <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-teal-500" style="font-size:20px">model_training</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['r2'] }}</p>
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">R² Score</p>
        <p class="text-[10px] text-slate-400 mt-1">0–1</p>
    </div>

</div>

@endsection
