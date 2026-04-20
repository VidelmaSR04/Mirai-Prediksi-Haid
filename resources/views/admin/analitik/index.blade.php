@extends('admin.layout')
@section('title', 'Analitik & Grafik')
@section('page-title', 'Analitik & Grafik Sistem (EDA)')
@section('search-placeholder', 'Cari data analitik...')

@section('content')

@php
$stats = $stats ?? [
    'total_user' => 0,
    'total_siklus' => 0,
    'rata_siklus' => 0,
    'persen_normal' => 0,
];

$stressData    = $stressData ?? [];
$sleepData     = $sleepData ?? [];
$bmiData       = $bmiData ?? [];
$prevCycleData = $prevCycleData ?? [];
$distribusi    = $distribusi ?? [];
$usiaBucket    = $usiaBucket ?? [];
@endphp

@if(isset($error))
<div class="mb-5 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-xl text-sm font-semibold">
    <span class="material-symbols-outlined text-primary">error</span>{{ $error }}
</div>
@endif

{{-- KPI --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-primary">group</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_user']) }}</p>
        <p class="text-[10px] font-bold uppercase text-slate-400">Total Pengguna</p>
    </div>

    <div class="bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-blue-400">history_edu</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_siklus']) }}</p>
        <p class="text-[10px] font-bold uppercase text-slate-400">Total Data Siklus</p>
    </div>

    <div class="bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-emerald-500">update</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['rata_siklus'] }}</p>
        <p class="text-[10px] font-bold uppercase text-slate-400">Rata-rata Siklus</p>
    </div>

    <div class="bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
            <span class="material-symbols-outlined text-amber-500">verified</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['persen_normal'] }}%</p>
        <p class="text-[10px] font-bold uppercase text-slate-400">% Normal</p>
    </div>
</div>

{{-- CHART --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    <div class="bg-white p-6 rounded-2xl border">
        <h3 class="font-bold mb-2">Stress vs Siklus</h3>
        <canvas id="stressChart"></canvas>
    </div>

    <div class="bg-white p-6 rounded-2xl border">
        <h3 class="font-bold mb-2">Tidur vs Siklus</h3>
        <canvas id="sleepChart"></canvas>
    </div>

    <div class="bg-white p-6 rounded-2xl border">
        <h3 class="font-bold mb-2">BMI vs Siklus</h3>
        <canvas id="bmiChart"></canvas>
    </div>

    <div class="bg-white p-6 rounded-2xl border">
        <h3 class="font-bold mb-2">Prev vs Current</h3>
        <canvas id="prevCycleChart"></canvas>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const stressData    = @json($stressData);
const sleepData     = @json($sleepData);
const bmiData       = @json($bmiData);
const prevCycleData = @json($prevCycleData);

function makeChart(id, data, labelX, labelY){
    if(!data || data.length === 0) return;

    new Chart(document.getElementById(id), {
        type: 'scatter',
        data: {
            datasets: [{
                data: data,
                pointRadius: 4
            }]
        },
        options: {
            scales: {
                x: { title: { display: true, text: labelX }},
                y: { title: { display: true, text: labelY }}
            }
        }
    });
}

makeChart('stressChart', stressData, 'Stress', 'Siklus');
makeChart('sleepChart', sleepData, 'Tidur', 'Siklus');
makeChart('bmiChart', bmiData, 'BMI', 'Siklus');
makeChart('prevCycleChart', prevCycleData, 'Prev', 'Current');

</script>
@endpush
