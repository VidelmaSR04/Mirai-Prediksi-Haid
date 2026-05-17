@extends('admin.layout')

@section('title', 'Data Pengguna')
@section('page-title', 'Manajemen Data Pengguna')
@section('search-placeholder', 'Cari pengguna...')

@section('content')

<div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">

    {{-- HEADER --}}
   <div class="p-6 border-b border-rose-50 flex justify-between items-center">
    <div>
        <h3 class="font-bold text-slate-800">Daftar Pengguna</h3>
        <p class="text-xs text-slate-400">Data pengguna sistem</p>
    </div>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('admin.pengguna') }}" class="flex gap-3">
        <select name="status" onchange="this.form.submit()"
            class="px-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-200">
            <option value="">Semua Status</option>
            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>
                Aktif
            </option>
            <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>
                Nonaktif
            </option>
        </select>
    </form>
</div>

    {{-- TABLE --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-rose-50/30 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-6 py-4">Avatar</th>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Telepon</th>
                    <th class="px-6 py-4 text-center">Usia</th>
                    <th class="px-6 py-4 text-center">BB</th>
                    <th class="px-6 py-4 text-center">TB</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-rose-50">
            @forelse($pageUsers ?? [] as $u)
                @php
                    $id     = $u['id_user'] ?? null;
                    $nama   = $u['nama_lengkap'] ?? 'User';
                    $email  = $u['email'] ?? '-';
                    $telp   = $u['no_telepon'] ?? '-';
                    $usia   = $u['usia'] ?? null;
                    $bb     = $u['berat_badan'] ?? null;
                    $tb     = $u['tinggi_badan'] ?? null;
                    $status = $u['status'] ?? 'Aktif';

                    $statusCls = strtolower($status) === 'aktif'
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'bg-rose-50 text-rose-700';
                @endphp

                <tr class="hover:bg-rose-50/10">
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-primary font-bold text-xs">
                            {{ strtoupper(substr($nama, 0, 2)) }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-slate-800">{{ $nama }}</p>
                        <p class="text-xs text-slate-400">ID: {{ $id ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $email }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $telp }}</td>
                    <td class="px-6 py-4 text-center">{{ $usia ? $usia.' thn' : '-' }}</td>
                    <td class="px-6 py-4 text-center">{{ $bb ? $bb.' kg' : '-' }}</td>
                    <td class="px-6 py-4 text-center">{{ $tb ? $tb.' cm' : '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 text-xs rounded-full border {{ $statusCls }}">
                            {{ $status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.pengguna.show', $id) }}"
                           class="p-2 hover:bg-slate-100 rounded-lg inline-block">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-10 text-slate-400">
                        Tidak ada data pengguna
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($totalPages > 1)
    <div class="px-6 py-4 border-t border-rose-50 flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Menampilkan {{ ($currentPage-1)*10 + 1 }} -
            {{ min($currentPage*10, $total) }} dari {{ $total }} data
        </p>

        <div class="flex gap-2">
            @if($currentPage > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage-1]) }}"
                   class="px-4 py-2 border rounded-xl text-sm hover:bg-slate-50">
                    ← Prev
                </a>
            @endif

            @if($currentPage < $totalPages)
                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage+1]) }}"
                   class="px-4 py-2 border rounded-xl text-sm hover:bg-slate-50">
                    Next →
                </a>
            @endif
        </div>
    </div>
    @endif

</div>

@endsection
