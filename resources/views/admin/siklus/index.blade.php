@extends('admin.layout')
@section('title', 'Data Siklus Menstruasi')
@section('page-title', 'Data Siklus Menstruasi')
@section('search-placeholder', 'Cari data siklus...')

@section('content')

@if(isset($error))
<div class="mb-5 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-xl text-sm font-semibold">
    <span class="material-symbols-outlined">error</span> {{ $error }}
</div>
@endif

{{-- ============================================================ --}}
{{-- STAT CARDS + CHART                                           --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Stats utama --}}
    <div class="lg:col-span-2 bg-accent-peach/10 p-7 rounded-2xl border border-accent-peach/20">

        {{-- Baris atas: rata-rata siklus & persen normal --}}
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Rata-rata Panjang Siklus</p>
                <h3 class="text-4xl font-bold mt-2 {{ ($rataRata ?? 0) > 0 ? 'text-primary' : 'text-slate-400' }}">
                    {{ $rataRata ?? 0 }}
                    <span class="text-lg font-medium text-slate-400">Hari</span>
                </h3>
                <p class="mt-2 text-sm text-slate-500">dari {{ number_format($total ?? 0) }} catatan siklus</p>
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

        {{-- Distribusi panjang siklus --}}
        <div class="grid grid-cols-3 gap-3">
            @php
                $distConfig = [
                    'Pendek (<21)'   => ['color' => 'text-rose-500',    'bg' => 'bg-rose-50',    'border' => 'border-rose-100'],
                    'Normal (21–35)' => ['color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-100'],
                    'Panjang (>35)'  => ['color' => 'text-blue-500',    'bg' => 'bg-blue-50',    'border' => 'border-blue-100'],
                ];
            @endphp
            @foreach($distribusiPanjang ?? [] as $label => $jumlah)
            @php $cfg = $distConfig[$label] ?? ['color' => 'text-slate-400', 'bg' => 'bg-slate-50', 'border' => 'border-slate-100']; @endphp
            <div class="bg-white rounded-xl p-4 text-center border {{ $cfg['border'] }}">
                <p class="text-2xl font-bold text-slate-800">{{ number_format($jumlah) }}</p>
                <p class="text-[10px] font-bold uppercase tracking-widest {{ $cfg['color'] }} mt-1">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Rata-rata Pain Level gauge --}}
    <div class="bg-white p-7 rounded-2xl border border-rose-100 shadow-sm flex flex-col">
        <h4 class="text-base font-bold text-slate-800 mb-1">Rata-rata Pain Level</h4>
        <p class="text-xs text-slate-400 mb-6">Skala 1–10 seluruh pengguna</p>
        <div class="flex-1 flex flex-col items-center justify-center gap-4">
            @php
                $pain = $rataRataPain ?? 0;
                $painPct = ($pain / 10) * 100;
                $painColor = $pain <= 3 ? '#10b981' : ($pain <= 6 ? '#f59e0b' : '#f43f5e');
                $painLabel = $pain <= 3 ? 'Ringan' : ($pain <= 6 ? 'Sedang' : 'Berat');
            @endphp
            <div class="relative w-32 h-32">
                <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f1f5f9" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9" fill="none"
                            stroke="{{ $painColor }}" stroke-width="3"
                            stroke-dasharray="{{ $painPct }} {{ 100 - $painPct }}"
                            stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-bold text-slate-800">{{ $pain }}</span>
                    <span class="text-[10px] text-slate-400">/10</span>
                </div>
            </div>
            <span class="px-4 py-1 text-xs font-bold rounded-full"
                  style="background:{{ $painColor }}20; color:{{ $painColor }}">
                {{ $painLabel }}
            </span>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- TABLE                                                        --}}
{{-- ============================================================ --}}
<div class="bg-white border border-rose-100 rounded-2xl overflow-hidden shadow-sm">

    {{-- Table header + search --}}
    <div class="p-7 flex items-center justify-between border-b border-rose-50 flex-wrap gap-4">
        <div>
            <h4 class="text-base font-bold text-slate-800">Catatan Siklus Pengguna</h4>
            <p class="text-xs text-slate-400 mt-0.5">
                {{ number_format($total ?? 0) }} entri data siklus historis
            </p>
        </div>
        <form method="GET" action="{{ route('admin.siklus') }}">
            @if(!empty($filterPain))
                <input type="hidden" name="pain" value="{{ $filterPain }}">
            @endif
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                      style="font-size:18px">search</span>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari nama atau ID..."
                       class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm
                              focus:ring-2 focus:ring-primary/20 outline-none w-64"
                       onchange="this.form.submit()"/>
            </div>
        </form>
    </div>

    {{-- Filter Pain Level Tabs --}}
    <div class="px-7 py-4 flex flex-wrap gap-2 border-b border-rose-50">
        @php
            $painTabs = [
                ''       => ['label' => 'Semua',            'active' => 'bg-primary text-white shadow-sm'],
                'ringan' => ['label' => 'Pain Ringan (1–3)', 'active' => 'bg-emerald-500 text-white shadow-sm'],
                'sedang' => ['label' => 'Pain Sedang (4–6)', 'active' => 'bg-amber-500 text-white shadow-sm'],
                'berat'  => ['label' => 'Pain Berat (7–10)', 'active' => 'bg-rose-500 text-white shadow-sm'],
            ];
        @endphp
        @foreach($painTabs as $val => $tab)
        <a href="{{ route('admin.siklus', array_filter(['pain' => $val ?: null, 'search' => $search ?? ''])) }}"
           class="px-5 py-2 text-sm font-semibold rounded-2xl transition-all
                  {{ ($filterPain ?? '') === $val
                        ? $tab['active']
                        : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            {{ $tab['label'] }}
        </a>
        @endforeach
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-rose-50/30 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-rose-50">
                <tr>
                    <th class="px-7 py-4">Pengguna</th>
                    <th class="px-5 py-4 text-center">Panjang Siklus</th>
                    <th class="px-5 py-4 text-center">Siklus Sebelumnya</th>
                    <th class="px-5 py-4 text-center">Pain Level</th>
                    <th class="px-5 py-4 text-center">Stress</th>
                    <th class="px-5 py-4 text-center">Tidur (jam)</th>
                    <th class="px-5 py-4 text-center">Mood</th>
                    <th class="px-5 py-4 text-center">Status Siklus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50 text-sm">
            @forelse($pageSiklus ?? [] as $s)
                @php
                    $panjang   = $s['cycle_length_days'] ?? 0;
                    $isNormal  = $panjang >= 21 && $panjang <= 35;
                    $pain      = $s['pain_level'] ?? 0;
                    $painColor = $pain <= 3
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                        : ($pain <= 6
                            ? 'bg-amber-50 text-amber-700 border-amber-100'
                            : 'bg-rose-50 text-rose-700 border-rose-100');
                @endphp
                <tr class="hover:bg-rose-50/20 transition-colors">

                    {{-- Pengguna --}}
                    <td class="px-7 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center
                                        text-primary font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($s['nama'] ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-700">{{ $s['nama'] ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">ID: {{ $s['user_id'] ?? '-' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Panjang siklus --}}
                    <td class="px-5 py-4 text-center font-bold text-slate-700">
                        {{ $panjang ? $panjang . ' hari' : '-' }}
                    </td>

                    {{-- Siklus sebelumnya --}}
                    <td class="px-5 py-4 text-center text-slate-600">
                        {{ $s['prev_cycle_length'] ? $s['prev_cycle_length'] . ' hari' : '-' }}
                    </td>

                    {{-- Pain level --}}
                    <td class="px-5 py-4 text-center">
                        <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $painColor }}">
                            {{ $pain ?? '-' }}
                        </span>
                    </td>

                    {{-- Stress --}}
                    <td class="px-5 py-4 text-center text-slate-600">
                        {{ $s['stress_score_cycle'] ?? '-' }}
                    </td>

                    {{-- Tidur --}}
                    <td class="px-5 py-4 text-center text-slate-600">
                        {{ $s['sleep_hours_cycle'] ?? '-' }}
                    </td>

                    {{-- Mood --}}
                    <td class="px-5 py-4 text-center">
                        @php $mood = $s['mood_score'] ?? null; @endphp
                        @if($mood !== null)
                            @php
                                $moodEmoji = match(true) {
                                    $mood >= 8  => ['😊', 'text-emerald-600'],
                                    $mood >= 5  => ['😐', 'text-amber-600'],
                                    default     => ['😞', 'text-rose-600'],
                                };
                            @endphp
                            <span class="{{ $moodEmoji[1] }} font-semibold">
                                {{ $moodEmoji[0] }} {{ $mood }}
                            </span>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>

                    {{-- Status siklus --}}
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
                    <td colspan="8" class="px-7 py-20 text-center text-slate-400">
                        <span class="material-symbols-outlined text-5xl block mb-3 opacity-40">calendar_month</span>
                        <p class="font-medium">Belum ada data siklus</p>
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
            Menampilkan
            {{ number_format((($currentPage ?? 1) - 1) * 10 + 1) }}–{{ number_format(min(($currentPage ?? 1) * 10, $total ?? 0)) }}
            dari {{ number_format($total ?? 0) }} data
        </p>
        <div class="flex items-center gap-2">
            @if(($currentPage ?? 1) > 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100
                      hover:bg-rose-50 text-slate-600 transition-colors">‹</a>
            @endif

            @for($i = max(1, ($currentPage ?? 1) - 2); $i <= min($totalPages ?? 1, ($currentPage ?? 1) + 2); $i++)
            <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl font-medium transition-colors
                      {{ $i === ($currentPage ?? 1)
                            ? 'bg-primary text-white shadow-sm'
                            : 'border border-rose-100 hover:bg-rose-50 text-slate-600' }}">
                {{ $i }}
            </a>
            @endfor

            @if(($currentPage ?? 1) < ($totalPages ?? 1))
            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100
                      hover:bg-rose-50 text-slate-600 transition-colors">›</a>
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
    const data = @json($distribusiPanjang ?? []);
    const labels = Object.keys(data);
    const values = Object.values(data);

    const colors = {
        'Pendek (<21)':   '#f43f5e',
        'Normal (21–35)': '#10b981',
        'Panjang (>35)':  '#3b82f6',
    };

    const total = values.reduce((a, b) => a + b, 0);
    if (!total) return;

    new Chart(document.getElementById('distribusiChart'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: labels.map(l => colors[l] ?? '#94a3b8'),
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
                        generateLabels: chart => chart.data.labels.map((label, i) => ({
                            text: `${label}  ${chart.data.datasets[0].data[i].toLocaleString('id-ID')}`,
                            fillStyle: chart.data.datasets[0].backgroundColor[i],
                            strokeStyle: chart.data.datasets[0].backgroundColor[i],
                            pointStyle: 'circle',
                            index: i,
                        }))
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString('id-ID')} (${((ctx.parsed/total)*100).toFixed(1)}%)`
                    }
                }
            }
        }
    });
});
</script>
@endpush
