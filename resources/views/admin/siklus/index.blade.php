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

    <div class="lg:col-span-2 bg-accent-peach/10 p-7 rounded-2xl border border-accent-peach/20">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Rata-rata Panjang Siklus</p>
                <h3 class="text-4xl font-bold mt-2 text-primary">{{ $rataRata ?? 0 }}
                    <span class="text-lg font-medium text-slate-400">Hari</span>
                </h3>
                <p class="mt-2 text-sm text-slate-500">dari {{ number_format($total ?? 0) }} catatan siklus</p>
            </div>
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Persentase Normal (21–35 hari)</p>
                <h3 class="text-4xl font-bold mt-2 text-slate-800">{{ $persenNormal ?? 0 }}%</h3>
                <div class="w-full bg-white rounded-full h-2.5 mt-4 overflow-hidden border border-rose-100">
                    <div class="bg-primary h-full rounded-full" style="width:{{ $persenNormal ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Distribusi Fase --}}
        <div class="grid grid-cols-5 gap-3 mt-8">
            @foreach($distribusi ?? [] as $fase => $jumlah)
            <div class="bg-white rounded-xl p-4 text-center border border-rose-100">
                <p class="text-2xl font-bold text-slate-800">{{ $jumlah }}</p>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mt-1">{{ $fase }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white p-7 rounded-2xl border border-rose-100 shadow-sm">
        <h4 class="text-base font-bold text-slate-800 mb-4">Distribusi Fase</h4>
        <canvas id="faseChart" height="220"></canvas>
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white border border-rose-100 rounded-2xl overflow-hidden shadow-sm">
    <div class="p-7 flex items-center justify-between border-b border-rose-50 flex-wrap gap-4">
        <div>
            <h4 class="text-base font-bold text-slate-800">Catatan Siklus Pengguna</h4>
            <p class="text-xs text-slate-400 mt-0.5">{{ number_format($total ?? 0) }} entri data siklus</p>
        </div>
        <form method="GET" action="{{ route('admin.siklus') }}">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:18px">search</span>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari nama..."
                       class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none w-64"
                       onchange="this.form.submit()"/>
            </div>
        </form>
    </div>

    <!-- BARU: Filter Fase -->
    <div class="px-7 pb-5 pt-2 flex flex-wrap gap-2 border-b border-rose-50">
        <a href="{{ route('admin.siklus') }}"
           class="px-5 py-2 text-sm rounded-2xl transition-all {{ empty($filterPhase) ? 'bg-primary text-white shadow-sm' : 'bg-white border border-slate-200 hover:bg-slate-50' }}">
           Semua Fase
        </a>
        @foreach(['Folikel', 'Ovulasi', 'Luteal', 'Menstruasi'] as $fase)
        <a href="{{ request()->fullUrlWithQuery(['fase' => $fase]) }}"
           class="px-5 py-2 text-sm rounded-2xl transition-all {{ $filterPhase === $fase ? 'bg-primary text-white shadow-sm' : 'bg-white border border-slate-200 hover:bg-slate-50' }}">
           {{ $fase }}
        </a>
        @endforeach
    </div>

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
        $panjang = (int)($s['panjang_siklus'] ?? 0);
        $isNormal = $panjang >= 21 && $panjang <= 35;

        $faseRaw = strtolower(trim($s['current_phase'] ?? ''));

        // mapping nama fase dari DB
        $faseMap = [
        'follicular' => 'Folikel',
        'folikel' => 'Folikel',
        'ovulation' => 'Ovulasi',
        'ovulasi' => 'Ovulasi',
        'luteal' => 'Luteal',
        'menstruation' => 'Menstruasi',
        'menstruasi' => 'Menstruasi',
        'period' => 'Menstruasi',
    ];

        $fase = $faseMap[$faseRaw] ?? 'Lainnya';

        $faseColor = match($fase) {
        'Folikel' => 'bg-pink-50 text-pink-600 border-pink-200',
        'Ovulasi' => 'bg-purple-50 text-purple-600 border-purple-200',
        'Luteal' => 'bg-blue-50 text-blue-600 border-blue-200',
        'Menstruasi' => 'bg-red-50 text-red-600 border-red-200',
        default => 'bg-slate-50 text-slate-500 border-slate-100'
        };
    @endphp
                
                <tr class="hover:bg-rose-50/10 transition-colors">
                    <td class="px-7 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-primary font-bold text-xs">
                                {{ strtoupper(substr($s['nama'] ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-700">{{ $s['nama'] ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">ID: {{ $s['id_user'] ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-slate-600">{{ $s['tanggal_mulai_haid'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-slate-600">{{ $s['tanggal_selesai_haid'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center font-bold text-slate-700">
                        {{ $panjang > 0 ? $panjang . ' hari' : '-' }}
                    </td>
                    <td class="px-5 py-4 text-center">{{ $s['pain_level'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center">{{ $s['stress_score_cycle'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center">{{ $s['sleep_hours_cycle'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $faseColor }}">
                            {{ $fase }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-3 py-1 text-xs font-bold rounded-full border
                            {{ $isNormal ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                            {{ $isNormal ? 'Normal' : 'Tidak Normal' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-7 py-20 text-center text-slate-400">
                        <span class="material-symbols-outlined text-5xl block mb-3 opacity-50">calendar_month</span>
                        Belum ada data siklus
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-7 py-5 border-t border-rose-50 flex items-center justify-between flex-wrap gap-3">
        <p class="text-xs text-slate-400">
            Menampilkan {{ (($currentPage ?? 1)-1)*10 + 1 }}–{{ min(($currentPage ?? 1)*10, $total ?? 0) }}
            dari {{ number_format($total ?? 0) }} data
        </p>
        <div class="flex items-center gap-2">
            @if(($currentPage ?? 1) > 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage-1]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 hover:bg-rose-50">
                ‹
            </a>
            @endif

            @for($i = max(1, ($currentPage ?? 1)-2); $i <= min($totalPages ?? 1, ($currentPage ?? 1)+2); $i++)
            <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl font-medium {{ $i === ($currentPage ?? 1) ? 'bg-primary text-white' : 'border border-rose-100 hover:bg-rose-50' }}">
                {{ $i }}
            </a>
            @endfor

            @if(($currentPage ?? 1) < ($totalPages ?? 1))
            <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage+1]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 hover:bg-rose-50">
                ›
            </a>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const distribusi = @json($distribusi ?? []);
if (Object.keys(distribusi).length > 0) {
    new Chart(document.getElementById('faseChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(distribusi),
            datasets: [{
                data: Object.values(distribusi),
                backgroundColor: ['#f90909', '#A855F7', '#3B82F6', '#e1c5c5', '#64748B'],
                
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 12 }, padding: 20 }
                }
            }
        }
    });
}
</script>
@endpush
