@extends('admin.layout')

@section('title', 'Data Pengguna')
@section('page-title', 'Manajemen Data Pengguna')
@section('search-placeholder', 'Cari pengguna...')

@section('content')

<div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">

```
{{-- HEADER --}}
<div class="p-6 border-b border-rose-50 flex justify-between items-center">
    <div>
        <h3 class="font-bold text-slate-800">Daftar Pengguna</h3>
        <p class="text-xs text-slate-400">Data pengguna sistem</p>
    </div>
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
            // SAFE DATA
            $id     = $u['id_user'] ?? null;
            $nama   = $u['nama_lengkap'] ?? 'User';
            $email  = $u['email'] ?? '-';
            $telp   = $u['no_telepon'] ?? '-';
            $usia   = $u['usia'] ?? null;
            $bb     = $u['berat_badan'] ?? null;
            $tb     = $u['tinggi_badan'] ?? null;
            $status = $u['status'] ?? 'Aktif';

            $statusCls = $status === 'Aktif'
                ? 'bg-emerald-50 text-emerald-600 border-emerald-100'
                : 'bg-rose-50 text-primary border-rose-100';
        @endphp

        <tr class="hover:bg-rose-50/10">

            {{-- Avatar --}}
            <td class="px-6 py-4">
                <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-primary font-bold text-xs">
                    {{ strtoupper(substr($nama, 0, 2)) }}
                </div>
            </td>

            {{-- Nama --}}
            <td class="px-6 py-4">
                <p class="font-semibold text-slate-800">{{ $nama }}</p>
                <p class="text-xs text-slate-400">ID: {{ $id ?? '-' }}</p>
            </td>

            {{-- Email --}}
            <td class="px-6 py-4 text-sm text-slate-600">
                {{ $email }}
            </td>

            {{-- Telepon --}}
            <td class="px-6 py-4 text-sm text-slate-600">
                {{ $telp }}
            </td>

            {{-- Usia --}}
            <td class="px-6 py-4 text-center">
                {{ $usia ? $usia.' thn' : '-' }}
            </td>

            {{-- BB --}}
            <td class="px-6 py-4 text-center">
                {{ $bb ? $bb.' kg' : '-' }}
            </td>

            {{-- TB --}}
            <td class="px-6 py-4 text-center">
                {{ $tb ? $tb.' cm' : '-' }}
            </td>

            {{-- Status --}}
            <td class="px-6 py-4 text-center">
                <span class="px-3 py-1 text-xs rounded-full border {{ $statusCls }}">
                    {{ $status }}
                </span>
            </td>

            {{-- Aksi --}}
            <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-2">

                    @if($id)
                    <a href="{{ route('admin.pengguna.show', $id) }}"
                       class="p-2 hover:bg-slate-100 rounded-lg">
                        <span class="material-symbols-outlined">visibility</span>
                    </a>
                    @endif

                    @if($id)
                    <form method="POST" action="{{ route('admin.pengguna.status', $id) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status"
                               value="{{ $status === 'Aktif' ? 'Nonaktif' : 'Aktif' }}">
                        <button class="p-2 hover:bg-rose-50 rounded-lg">
                            <span class="material-symbols-outlined">
                                {{ $status === 'Aktif' ? 'block' : 'check_circle' }}
                            </span>
                        </button>
                    </form>
                    @endif

                </div>
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
```

</div>

@endsection
