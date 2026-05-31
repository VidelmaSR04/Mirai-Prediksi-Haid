@extends('admin.layout')
@section('title', 'Data Siklus Menstruasi')
@section('page-title', 'Data Siklus Menstruasi')
@section('search-placeholder', 'Cari data siklus...')

@section('content')

@if(isset($error))
<div class="mb-5 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-xl text-sm font-semibold">
    <span class="material-symbols-outlined text-primary">error</span> {{ $error }}
</div>
@endif

{{-- STAT + CHART --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Stats Card --}}
    <div class="lg:col-span-2 bg-accent-peach/10 p-7 rounded-2xl border border-accent-peach/20">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Rata-rata Panjang Siklus</p>
                <h3 class="text-4xl font-bold mt-2 {{ ($rataRata ?? 0) > 0 ? 'text-primary' : 'text-slate-400' }}">
                    {{ $rataRata ?? 0 }}
                    <span class="text-lg font-medium text-slate-400">Hari</span>
                </h3>
                <p class="mt-2 text-sm text-slate-500">dari {{ number_format($total ?? 0) }} catatan siklus</p>
                @if(!empty($filterPhase))
                    <p class="mt-1 text-xs text-primary font-semibold">Filter: {{ $filterPhase }}</p>
                @endif
            </div>
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Persentase Normal (21–35 hari)</p>
                <h3 class="text-4xl font-bold mt-2 text-slate-800">{{ $persenNormal ?? 0 }}%</h3>
                <div class="w-full bg-white rounded-full h-2.5 mt-4 overflow-hidden border border-rose-100">
                    <div class="bg-primary h-full rounded-full transition-all duration-500"
                         style="width:{{ $persenNormal ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Distribusi Fase — selalu total keseluruhan --}}
        <div class="grid grid-cols-5 gap-3 mt-8">
            @php
                $faseIcons = [
                    'Folikel'    => ['icon' => 'psychiatry',    'color' => 'text-pink-500',   'bg' => 'bg-pink-50',   'border' => 'border-pink-100'],
                    'Ovulasi'    => ['icon' => 'circle',        'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'border' => 'border-purple-100'],
                    'Luteal'     => ['icon' => 'nightlight',    'color' => 'text-blue-500',   'bg' => 'bg-blue-50',   'border' => 'border-blue-100'],
                    'Menstruasi' => ['icon' => 'water_drop',    'color' => 'text-rose-500',   'bg' => 'bg-rose-50',   'border' => 'border-rose-100'],
                    'Lainnya'    => ['icon' => 'more_horiz',    'color' => 'text-slate-400',  'bg' => 'bg-slate-50',  'border' => 'border-slate-100'],
                ];
                $distribusiAll = $distribusi ?? [];
                // Pastikan semua 5 fase selalu tampil walau 0
                $allFase = ['Folikel' => 0, 'Ovulasi' => 0, 'Luteal' => 0, 'Menstruasi' => 0, 'Lainnya' => 0];
                foreach ($allFase as $f => $default) {
                    $allFase[$f] = $distribusiAll[$f] ?? 0;
                }
            @endphp
            @foreach($allFase as $fase => $jumlah)
            @php $fi = $faseIcons[$fase] ?? $faseIcons['Lainnya']; @endphp
            <div class="bg-white rounded-xl p-4 text-center border {{ $fi['border'] }} 
                        {{ (!empty($filterPhase) && strtolower($filterPhase) === strtolower($fase)) ? 'ring-2 ring-primary/30' : '' }}">
                <p class="text-2xl font-bold text-slate-800">{{ number_format($jumlah) }}</p>
                <p class="text-[10px] font-bold uppercase tracking-widest {{ $fi['color'] }} mt-1">{{ $fase }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Donut Chart --}}
    <div class="bg-white p-7 rounded-2xl border border-rose-100 shadow-sm flex flex-col">
        <h4 class="text-base font-bold text-slate-800 mb-1">Distribusi Fase</h4>
        <p class="text-xs text-slate-400 mb-4">Keseluruhan data</p>
        <div class="flex-1 flex items-center justify-center">
            <canvas id="faseChart" style="max-height:220px"></canvas>
        </div>
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white border border-rose-100 rounded-2xl overflow-hidden shadow-sm">
    <div class="p-7 flex items-center justify-between border-b border-rose-50 flex-wrap gap-4">
        <div>
            <h4 class="text-base font-bold text-slate-800">Catatan Siklus Pengguna</h4>
            <p class="text-xs text-slate-400 mt-0.5">{{ number_format($total ?? 0) }} entri data siklus
                @if(!empty($filterPhase))
                    <span class="ml-1 text-primary font-semibold">· Filter: {{ $filterPhase }}</span>
                @endif
            </p>
        </div>
        <form method="GET" action="{{ route('admin.siklus') }}">
            @if(!empty($filterPhase))
                <input type="hidden" name="fase" value="{{ $filterPhase }}">
            @endif
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                      style="font-size:18px">search</span>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari nama..."
                       class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none w-64"
                       onchange="this.form.submit()"/>
            </div>
        </form>
    </div>

    {{-- Filter Fase Tabs --}}
    <div class="px-7 pb-4 pt-4 flex flex-wrap gap-2 border-b border-rose-50">
        <a href="{{ route('admin.siklus', array_filter(['search' => $search ?? ''])) }}"
           class="px-5 py-2 text-sm font-semibold rounded-2xl transition-all
                  {{ empty($filterPhase) ? 'bg-primary text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
           Semua Fase
        </a>
        @foreach(['Folikel', 'Ovulasi', 'Luteal', 'Menstruasi'] as $fase)
        @php
            $faseActiveColors = [
                'Folikel'    => 'bg-pink-500 text-white shadow-sm',
                'Ovulasi'    => 'bg-purple-500 text-white shadow-sm',
                'Luteal'     => 'bg-blue-500 text-white shadow-sm',
                'Menstruasi' => 'bg-rose-500 text-white shadow-sm',
            ];
            $isActive = ($filterPhase === $fase);
        @endphp
        <a href="{{ route('admin.siklus', array_filter(['fase' => $fase, 'search' => $search ?? ''])) }}"
           class="px-5 py-2 text-sm font-semibold rounded-2xl transition-all
                  {{ $isActive ? ($faseActiveColors[$fase] ?? 'bg-primary text-white') : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
           {{ $fase }}
        </a>
        @endforeach
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-rose-50/30 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-rose-50">
                <tr>
                    <th class="px-7 py-4">Pengguna</th>
                    <th class="px-5 py-4">Tgl Mulai</th>
                    <th class="px-5 py-4">Tgl Selesai</th>
                    <th class="px-5 py-4 text-center">Panjang Siklus</th>
                    <th class="px-5 py-4 text-center">Pain Level</th>
                    <th class="px-5 py-4 text-center">Stress</th>
                    <th class="px-5 py-4 text-center">Tidur (jam)</th>
                    <th class="px-5 py-4 text-center">Fase</th>
                    <th class="px-5 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50 text-sm">
            @forelse($pageSiklus ?? [] as $s)
                @php
                    $panjang  = (int)($s['panjang_siklus'] ?? 0);
                    $isNormal = $panjang >= 21 && $panjang <= 35;

                    // current_phase sudah dinormalisasi oleh controller
                    $fase = $s['current_phase'] ?? 'Lainnya';

                    $faseColor = match($fase) {
                        'Folikel'    => 'bg-pink-50 text-pink-600 border-pink-200',
                        'Ovulasi'    => 'bg-purple-50 text-purple-600 border-purple-200',
                        'Luteal'     => 'bg-blue-50 text-blue-600 border-blue-200',
                        'Menstruasi' => 'bg-rose-50 text-rose-600 border-rose-200',
                        default      => 'bg-slate-50 text-slate-500 border-slate-100',
                    };
                @endphp
                <tr class="hover:bg-rose-50/20 transition-colors">
                    <td class="px-7 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-primary font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($s['nama'] ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-700">{{ $s['nama'] ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">ID: {{ $s['id_user'] ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-slate-600 whitespace-nowrap">{{ $s['tanggal_mulai_haid'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-slate-600 whitespace-nowrap">{{ $s['tanggal_selesai_haid'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center font-bold text-slate-700">
                        {{ $panjang > 0 ? $panjang . ' hari' : '-' }}
                    </td>
                    <td class="px-5 py-4 text-center text-slate-600">{{ $s['pain_level'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center text-slate-600">{{ $s['stress_score_cycle'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center text-slate-600">{{ $s['sleep_hours_cycle'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $faseColor }}">
                            {{ $fase }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-3 py-1 text-xs font-bold rounded-full border
                            {{ $isNormal
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                            {{ $isNormal ? 'Normal' : 'Tidak Normal' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-7 py-20 text-center text-slate-400">
                        <span class="material-symbols-outlined text-5xl block mb-3 opacity-40">calendar_month</span>
                        <p class="font-medium">Belum ada data siklus</p>
                        @if(!empty($filterPhase))
                            <p class="text-xs mt-1">untuk fase <strong>{{ $filterPhase }}</strong></p>
                        @endif
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(($totalPages ?? 1) > 1)
    <div class="px-7 py-5 border-t border-rose-50 flex items-center justify-between flex-wrap gap-3">
        <p class="text-xs text-slate-400">
            Menampilkan {{ number_format((($currentPage ?? 1) - 1) * 10 + 1) }}–{{ number_format(min(($currentPage ?? 1) * 10, $total ?? 0)) }}
            dari {{ number_format($total ?? 0) }} data
        </p>
        <div class="flex items-center gap-2">
            @if(($currentPage ?? 1) > 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 hover:bg-rose-50 text-slate-600 transition-colors">
                ‹
            </a>
            @endif

            @for($i = max(1, ($currentPage ?? 1) - 2); $i <= min($totalPages ?? 1, ($currentPage ?? 1) + 2); $i++)
            <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl font-medium transition-colors
                      {{ $i === ($currentPage ?? 1) ? 'bg-primary text-white shadow-sm' : 'border border-rose-100 hover:bg-rose-50 text-slate-600' }}">
                {{ $i }}
            </a>
            @endfor

            @if(($currentPage ?? 1) < ($totalPages ?? 1))
            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 hover:bg-rose-50 text-slate-600 transition-colors">
                ›
            </a>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data distribusi dari controller (sudah ternormalisasi, semua fase)
    const distribusi = @json($distribusi ?? []);

    // Warna per fase — urutan sesuai key distribusi
    const warnaMeta = {
        'Folikel':    { color: '#EC4899', bg: 'rgba(236,72,153,0.15)' },  // pink
        'Ovulasi':    { color: '#A855F7', bg: 'rgba(168,85,247,0.15)' },  // purple
        'Luteal':     { color: '#3B82F6', bg: 'rgba(59,130,246,0.15)' },  // blue
        'Menstruasi': { color: '#F43F5E', bg: 'rgba(244,63,94,0.15)'  },  // rose
        'Lainnya':    { color: '#94A3B8', bg: 'rgba(148,163,184,0.15)' }, // slate
    };

    const labels = Object.keys(distribusi);
    const data   = Object.values(distribusi);

    // Jangan render chart jika semua 0
    const total = data.reduce((a, b) => a + b, 0);
    if (total === 0) {
        document.getElementById('faseChart').closest('div').innerHTML =
            '<p class="text-slate-400 text-sm text-center py-10">Belum ada data</p>';
        return;
    }

    const backgroundColor  = labels.map(l => warnaMeta[l]?.color ?? '#94A3B8');
    const hoverBg          = labels.map(l => warnaMeta[l]?.color ?? '#94A3B8');

    new Chart(document.getElementById('faseChart'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColor,
                hoverBackgroundColor: hoverBg,
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11, weight: '600' },
                        padding: 14,
                        usePointStyle: true,
                        pointStyleWidth: 10,
                        generateLabels: function(chart) {
                            const meta = chart.getDatasetMeta(0);
                            return chart.data.labels.map((label, i) => ({
                                text: `${label}  ${chart.data.datasets[0].data[i].toLocaleString('id-ID')}`,
                                fillStyle: chart.data.datasets[0].backgroundColor[i],
                                strokeStyle: chart.data.datasets[0].backgroundColor[i],
                                pointStyle: 'circle',
                                hidden: meta.data[i]?.hidden ?? false,
                                index: i,
                            }));
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const val = ctx.parsed;
                            const pct = ((val / total) * 100).toFixed(1);
                            return ` ${ctx.label}: ${val.toLocaleString('id-ID')} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
