@extends('admin.layout')
@section('title', 'Data Siklus Menstruasi')
@section('page-title', 'Data Siklus Menstruasi')
@section('search-placeholder', 'Cari data siklus...')

@section('content')

@if(isset($error))
<div class="mb-5 flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-xl text-sm font-semibold">
    <span class="material-symbols-outlined text-primary">error</span>{{ $error }}
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

        {{-- Distribusi fase --}}
        <div class="grid grid-cols-4 gap-3 mt-6">
            @foreach($distribusi ?? [] as $fase => $jumlah)
            <div class="bg-white rounded-xl p-3 text-center border border-rose-100">
                <p class="text-lg font-bold text-slate-800">{{ $jumlah }}</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-0.5">{{ $fase }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white p-7 rounded-2xl border border-rose-100 shadow-sm">
        <h4 class="text-base font-bold text-slate-800 mb-4">Distribusi Fase</h4>
        <canvas id="faseChart" height="200"></canvas>
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
                       class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none w-48"
                       onchange="this.form.submit()"/>
            </div>
        </form>
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
    // Panjang siklus (SUDAH FIX alias)
    $panjang = isset($s['panjang_siklus']) ? (int)$s['panjang_siklus'] : 0;

    // Status normal
    $isNormal = $panjang >= 21 && $panjang <= 35;

    // Fase (AMANKAN dulu sebelum dipakai)
    $faseRaw = $s['current_phase'] ?? ($s['pattern'] ?? '');
    $faseKey = strtolower(trim($faseRaw));

    // Default fase tampil
    $fase = $faseKey ? ucfirst($faseKey) : '-';

    // Warna fase (ANTI ERROR)
    switch ($faseKey) {
        case 'menstruasi':
            $faseColor = 'bg-red-50 text-red-500 border-red-100';
            break;
        case 'folikel':
            $faseColor = 'bg-amber-50 text-amber-600 border-amber-100';
            break;
        case 'ovulasi':
            $faseColor = 'bg-emerald-50 text-emerald-600 border-emerald-100';
            break;
        case 'luteal':
            $faseColor = 'bg-violet-50 text-violet-600 border-violet-100';
            break;
        default:
            $faseColor = 'bg-slate-50 text-slate-500 border-slate-100';
            break;
    }
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
                    <td class="px-5 py-4 text-slate-600">{{ $s['tanggal_mulai_haid']   ?? '-' }}</td>
                    <td class="px-5 py-4 text-slate-600">{{ $s['tanggal_selesai_haid'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center font-bold text-slate-700">
                        {{ $panjang > 0 ? $panjang.' hari' : '-' }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        @php $pain = (int)($s['pain_level'] ?? 0); @endphp
                        <div class="flex items-center justify-center gap-1">
                            <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" style="width:{{ $pain * 10 }}%"></div>
                            </div>
                            <span class="text-xs text-slate-500">{{ $pain }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center text-slate-600">{{ $s['stress_score_cycle'] ?? '-' }}</td>
                    <td class="px-5 py-4 text-center text-slate-600">{{ $s['sleep_hours_cycle']  ?? '-' }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full border {{ $faseColor }}">
                            {{ $fase }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full border
                            {{ $isNormal ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-primary border-rose-100' }}">
                            {{ $isNormal ? 'Normal' : 'Tidak Normal' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-7 py-16 text-center text-slate-400">
                        <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">calendar_month</span>
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
            Menampilkan {{ (($currentPage ?? 1)-1)*10+1 }}–{{ min(($currentPage ?? 1)*10, $total ?? 0) }}
            dari {{ number_format($total ?? 0) }} data
        </p>
        <div class="flex items-center gap-2">
            @if(($currentPage ?? 1) > 1)
            <a href="{{ request()->fullUrlWithQuery(['page'=>($currentPage-1)]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 text-slate-400 hover:bg-rose-50 hover:text-primary transition-all">
                <span class="material-symbols-outlined">chevron_left</span>
            </a>
            @endif
            @for($i = max(1, ($currentPage ?? 1)-2); $i <= min($totalPages ?? 1, ($currentPage ?? 1)+2); $i++)
            <a href="{{ request()->fullUrlWithQuery(['page'=>$i]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl font-bold text-sm transition-all
                      {{ $i === ($currentPage ?? 1) ? 'bg-primary text-white shadow-md shadow-primary/20' : 'border border-rose-100 text-slate-600 hover:bg-rose-50' }}">
                {{ $i }}
            </a>
            @endfor
            @if(($currentPage ?? 1) < ($totalPages ?? 1))
            <a href="{{ request()->fullUrlWithQuery(['page'=>($currentPage+1)]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl border border-rose-100 text-slate-400 hover:bg-rose-50 hover:text-primary transition-all">
                <span class="material-symbols-outlined">chevron_right</span>
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
                backgroundColor: ['#FFB7A5','#E35D6A','#7C3AED','#F59E0B'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
        }
    });
}
</script>
@endpush
