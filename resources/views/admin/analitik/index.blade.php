@extends('admin.layout')
@section('title', 'Analitik & Grafik')
@section('page-title', 'Analitik & Grafik Sistem (EDA)')
@section('search-placeholder', 'Cari data analitik...')

@section('content')

@php
$stats = $stats ?? ['total_user' => 0, 'total_siklus' => 0, 'rata_siklus' => 0, 'persen_normal' => 0];
$histogramData = $histogramData ?? ['labels' => [], 'values' => []];
$faseBarData   = $faseBarData   ?? [];
$stressPerFase = $stressPerFase ?? [];
$sleepPerFase  = $sleepPerFase  ?? [];
$trenData      = $trenData      ?? ['labels' => [], 'values' => []];
$painData      = $painData      ?? ['labels' => [], 'values' => []];
$statusData    = $statusData    ?? [];
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

{{-- ROW 1: Histogram + Tren Bulanan --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Chart 1: Histogram Panjang Siklus --}}
    <div class="bg-white p-6 rounded-2xl border border-rose-100 shadow-sm">
        <div class="flex items-start justify-between mb-1">
            <div>
                <h3 class="font-bold text-slate-800">Distribusi Panjang Siklus</h3>
                <p class="text-xs text-slate-400 mt-0.5">Frekuensi panjang siklus (hari) seluruh pengguna</p>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 bg-rose-50 text-primary rounded-lg">HISTOGRAM</span>
        </div>
        <div class="flex gap-4 mt-2 mb-4 text-xs">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-emerald-400 inline-block"></span>
                <span class="text-slate-500">Normal (21–35 hari)</span>
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-rose-400 inline-block"></span>
                <span class="text-slate-500">Di luar normal</span>
            </span>
        </div>
        <canvas id="histogramChart" height="190"></canvas>
        <p class="text-[11px] text-slate-400 mt-3 text-center">
            💡 Bar tinggi di tengah = mayoritas siklus dalam rentang normal (21–35 hari)
        </p>
    </div>

    {{-- Chart 2: Tren Rata-rata Siklus per Bulan --}}
    <div class="bg-white p-6 rounded-2xl border border-rose-100 shadow-sm">
        <div class="flex items-start justify-between mb-1">
            <div>
                <h3 class="font-bold text-slate-800">Tren Rata-rata Siklus (12 Bln Terakhir)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Rata-rata panjang siklus per bulan</p>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 bg-blue-50 text-blue-500 rounded-lg">TREN</span>
        </div>
        <canvas id="trenChart" height="210"></canvas>
        <p class="text-[11px] text-slate-400 mt-3 text-center">
            💡 Garis stabil = pola siklus konsisten antar bulan
        </p>
    </div>

</div>

{{-- ROW 2: Rata-rata per Fase + Status Normal --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Chart 3: Rata-rata Panjang Siklus per Fase --}}
    <div class="bg-white p-6 rounded-2xl border border-rose-100 shadow-sm lg:col-span-2">
        <div class="flex items-start justify-between mb-1">
            <div>
                <h3 class="font-bold text-slate-800">Perbandingan Rata-rata per Fase</h3>
                <p class="text-xs text-slate-400 mt-0.5">Rata-rata panjang siklus, stress, dan jam tidur per fase menstruasi</p>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 bg-purple-50 text-purple-500 rounded-lg">BAR</span>
        </div>
        <canvas id="faseGroupedChart" height="175"></canvas>
        <p class="text-[11px] text-slate-400 mt-3 text-center">
            💡 Bandingkan 3 metrik sekaligus antar fase untuk melihat pola kesehatan
        </p>
    </div>

    {{-- Chart 4: Donut Status Normal --}}
    <div class="bg-white p-6 rounded-2xl border border-rose-100 shadow-sm">
        <div class="flex items-start justify-between mb-1">
            <div>
                <h3 class="font-bold text-slate-800">Status Siklus</h3>
                <p class="text-xs text-slate-400 mt-0.5">Normal vs tidak normal</p>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 bg-emerald-50 text-emerald-500 rounded-lg">DONUT</span>
        </div>
        <canvas id="statusChart" height="195"></canvas>
        @php $totalStatus = array_sum($statusData ?? []); @endphp
        <div class="mt-3 grid grid-cols-2 gap-2 text-center">
            <div class="bg-emerald-50 rounded-xl p-2">
                <p class="text-lg font-bold text-emerald-600">{{ number_format($statusData['Normal'] ?? 0) }}</p>
                <p class="text-[10px] text-emerald-500 font-bold">Normal</p>
            </div>
            <div class="bg-rose-50 rounded-xl p-2">
                <p class="text-lg font-bold text-rose-500">{{ number_format($statusData['Tidak Normal'] ?? 0) }}</p>
                <p class="text-[10px] text-rose-400 font-bold">Tidak Normal</p>
            </div>
        </div>
    </div>

</div>

{{-- ROW 3: Pain Level + Stress & Tidur per Fase --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Chart 5: Distribusi Pain Level --}}
    <div class="bg-white p-6 rounded-2xl border border-rose-100 shadow-sm">
        <div class="flex items-start justify-between mb-1">
            <div>
                <h3 class="font-bold text-slate-800">Distribusi Pain Level</h3>
                <p class="text-xs text-slate-400 mt-0.5">Jumlah catatan per tingkat nyeri (1–10)</p>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 bg-amber-50 text-amber-500 rounded-lg">BAR</span>
        </div>
        <canvas id="painChart" height="195"></canvas>
        <p class="text-[11px] text-slate-400 mt-3 text-center">
            💡 Bar paling tinggi = level nyeri yang paling sering dialami pengguna
        </p>
    </div>

    {{-- Chart 6: Stress & Tidur per Fase --}}
    <div class="bg-white p-6 rounded-2xl border border-rose-100 shadow-sm">
        <div class="flex items-start justify-between mb-1">
            <div>
                <h3 class="font-bold text-slate-800">Stress & Tidur per Fase</h3>
                <p class="text-xs text-slate-400 mt-0.5">Rata-rata skor stress dan jam tidur per fase</p>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 bg-rose-50 text-primary rounded-lg">BAR</span>
        </div>
        <canvas id="stressSleepChart" height="195"></canvas>
        <p class="text-[11px] text-slate-400 mt-3 text-center">
            💡 Fase dengan stress tinggi biasanya punya jam tidur lebih rendah
        </p>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const faseLabels = ['Folikel', 'Ovulasi', 'Luteal', 'Menstruasi'];
    const faseColors = {
        'Folikel':    '#EC4899',
        'Ovulasi':    '#A855F7',
        'Luteal':     '#3B82F6',
        'Menstruasi': '#F43F5E',
    };

    // ── 1. HISTOGRAM: Distribusi Panjang Siklus ──────────────────────────────
    const histLabels = @json($histogramData['labels'] ?? []);
    const histValues = @json($histogramData['values'] ?? []);

    const histColors = histLabels.map(label => {
        const start = parseInt(label.split('-')[0]);
        return (start >= 21 && start + 2 <= 35) ? '#34D399' : '#FB7185';
    });

    new Chart(document.getElementById('histogramChart'), {
        type: 'bar',
        data: {
            labels: histLabels,
            datasets: [{
                label: 'Jumlah Siklus',
                data: histValues,
                backgroundColor: histColors,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y.toLocaleString('id-ID')} catatan`,
                        title: ctx => `Panjang siklus: ${ctx[0].label} hari`
                    }
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Rentang Panjang Siklus (hari)', font: { size: 11 } },
                    ticks: { font: { size: 10 } }
                },
                y: {
                    title: { display: true, text: 'Jumlah Catatan', font: { size: 11 } },
                    ticks: {
                        font: { size: 10 },
                        callback: v => v.toLocaleString('id-ID')
                    }
                }
            }
        }
    });

    // ── 2. LINE: Tren Rata-rata Siklus Bulanan ────────────────────────────────
    const trenLabels = @json($trenData['labels'] ?? []);
    const trenValues = @json($trenData['values'] ?? []);

    new Chart(document.getElementById('trenChart'), {
        type: 'line',
        data: {
            labels: trenLabels,
            datasets: [{
                label: 'Rata-rata Siklus (hari)',
                data: trenValues,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#3B82F6',
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` Rata-rata: ${ctx.parsed.y} hari`
                    }
                }
            },
            scales: {
                x: { ticks: { font: { size: 10 } } },
                y: {
                    title: { display: true, text: 'Hari', font: { size: 11 } },
                    min: 20,
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    // ── 3. BAR GROUPED: Rata-rata Siklus, Stress, Tidur per Fase ─────────────
    const faseBarData    = @json($faseBarData ?? []);
    const stressPerFase  = @json($stressPerFase ?? []);
    const sleepPerFase   = @json($sleepPerFase ?? []);

    const faseSiklusVals = faseLabels.map(f => faseBarData[f] ?? 0);
    const faseStressVals = faseLabels.map(f => stressPerFase[f] ?? 0);
    const faseSleepVals  = faseLabels.map(f => sleepPerFase[f] ?? 0);

    new Chart(document.getElementById('faseGroupedChart'), {
        type: 'bar',
        data: {
            labels: faseLabels,
            datasets: [
                {
                    label: 'Rata-rata Siklus (hari)',
                    data: faseSiklusVals,
                    backgroundColor: 'rgba(59,130,246,0.75)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Rata-rata Stress (1-10)',
                    data: faseStressVals,
                    backgroundColor: 'rgba(244,63,94,0.75)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Rata-rata Tidur (jam)',
                    data: faseSleepVals,
                    backgroundColor: 'rgba(168,85,247,0.75)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 11 }, usePointStyle: true, pointStyleWidth: 10 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}`
                    }
                }
            },
            scales: {
                x: { ticks: { font: { size: 11 } } },
                y: {
                    title: { display: true, text: 'Nilai', font: { size: 11 } },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    // ── 4. DONUT: Status Normal vs Tidak Normal ───────────────────────────────
    const statusData = @json($statusData ?? []);
    const statusLabels = Object.keys(statusData);
    const statusValues = Object.values(statusData);
    const statusTotal  = statusValues.reduce((a, b) => a + b, 0);

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: ['#34D399', '#FB7185'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const pct = ((ctx.parsed / statusTotal) * 100).toFixed(1);
                            return ` ${ctx.label}: ${ctx.parsed.toLocaleString('id-ID')} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

    // ── 5. BAR HORIZONTAL: Pain Level ────────────────────────────────────────
    const painLabels = @json($painData['labels'] ?? []);
    const painValues = @json($painData['values'] ?? []);

    const painColors = painLabels.map((_, i) => {
        const lvl = i + 1;
        if (lvl <= 3)       return '#34D399'; // rendah → hijau
        else if (lvl <= 6)  return '#FBBF24'; // sedang → kuning
        else                return '#FB7185'; // tinggi → merah
    });

    new Chart(document.getElementById('painChart'), {
        type: 'bar',
        data: {
            labels: painLabels,
            datasets: [{
                label: 'Jumlah Catatan',
                data: painValues,
                backgroundColor: painColors,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.x.toLocaleString('id-ID')} catatan`
                    }
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Jumlah Catatan', font: { size: 11 } },
                    ticks: {
                        font: { size: 10 },
                        callback: v => v.toLocaleString('id-ID')
                    }
                },
                y: { ticks: { font: { size: 11 } } }
            }
        }
    });

    // ── 6. BAR: Stress & Tidur per Fase ──────────────────────────────────────
    new Chart(document.getElementById('stressSleepChart'), {
        type: 'bar',
        data: {
            labels: faseLabels,
            datasets: [
                {
                    label: 'Rata-rata Stress (1-10)',
                    data: faseStressVals,
                    backgroundColor: 'rgba(244,63,94,0.8)',
                    borderRadius: 6,
                    borderSkipped: false,
                    yAxisID: 'yStress',
                },
                {
                    label: 'Rata-rata Tidur (jam)',
                    data: faseSleepVals,
                    backgroundColor: 'rgba(99,102,241,0.8)',
                    borderRadius: 6,
                    borderSkipped: false,
                    yAxisID: 'ySleep',
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 11 }, usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}`
                    }
                }
            },
            scales: {
                x: { ticks: { font: { size: 11 } } },
                yStress: {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: 'Stress (1-10)', font: { size: 10 } },
                    min: 0, max: 10,
                    ticks: { font: { size: 10 } },
                },
                ySleep: {
                    type: 'linear',
                    position: 'right',
                    title: { display: true, text: 'Tidur (jam)', font: { size: 10 } },
                    min: 0, max: 12,
                    ticks: { font: { size: 10 } },
                    grid: { drawOnChartArea: false },
                },
            }
        }
    });

});
</script>
@endpush
