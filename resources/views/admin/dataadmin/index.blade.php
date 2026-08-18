@extends('admin.layout')

@section('page-title', 'Data Admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Data Admin</h1>

    <div class="bg-white rounded-xl shadow-sm">
        {{-- Header card: judul + kotak pencarian --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-800">Daftar Admin</h2>
                <p class="text-sm text-gray-400">Data administrator sistem</p>
            </div>

            <form method="GET" action="{{ route('admin.data-admin.index') }}" class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Filter berdasarkan ID, nama, atau email..."
                       class="pl-9 pr-4 py-2 w-80 border border-gray-200 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-pink-200">
            </form>
        </div>

        {{-- Tabel data admin --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 text-xs tracking-wide">
                    <th class="px-6 py-3 font-medium">AVATAR</th>
                    <th class="px-6 py-3 font-medium">NAMA</th>
                    <th class="px-6 py-3 font-medium">EMAIL</th>
                    <th class="px-6 py-3 font-medium text-right">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $index => $admin)
                    <tr class="border-t border-gray-50 hover:bg-pink-50/30">
                        {{-- Avatar bulat pink dengan inisial --}}
                        <td class="px-6 py-4">
                            <div class="w-9 h-9 rounded-full bg-pink-500 text-white flex items-center
                                        justify-center text-xs font-semibold">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </div>
                        </td>

                        {{-- Nama + ID urut --}}
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $admin->name }}</div>
                            <div class="text-xs text-gray-400">ID: {{ $index + 1 }}</div>
                        </td>

                        {{-- Email --}}
                        <td class="px-6 py-4 text-pink-500">{{ $admin->email }}</td>

                        {{-- Aksi: ikon mata buka popup --}}
                        <td class="px-6 py-4 text-right">
                            <button type="button"
                               onclick="showAdminDetail('{{ $admin->_id }}')"
                               class="text-pink-400 hover:text-pink-600 inline-block">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                            Belum ada data admin.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════ MODAL POPUP DETAIL ADMIN ══════════════ --}}
<div id="admin-detail-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    {{-- Overlay gelap, klik di luar box untuk tutup --}}
    <div class="absolute inset-0 bg-black/40" onclick="closeAdminDetail()"></div>

    {{-- Box modal --}}
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6 z-10">
        <button type="button" onclick="closeAdminDetail()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>

        {{-- Loading state --}}
        <div id="admin-detail-loading" class="text-center text-gray-400 py-10">
            Memuat data...
        </div>

        {{-- Isi detail (disembunyikan sampai data siap) --}}
        <div id="admin-detail-body" class="hidden">
            <div class="flex flex-col items-center mb-6">
                <div id="admin-detail-avatar"
                     class="w-16 h-16 rounded-full bg-pink-500 text-white flex items-center
                            justify-center text-xl font-bold mb-3"></div>
                <h3 id="admin-detail-name" class="font-bold text-gray-800 text-lg"></h3>
            </div>

            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase font-medium">Email</p>
                    <p id="admin-detail-email" class="text-gray-700 font-medium"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-medium">Terdaftar Sejak</p>
                    <p id="admin-detail-created" class="text-gray-700 font-medium"></p>
                </div>
            </div>
        </div>
    </div>
</div>@push('scripts')
<script>
function showAdminDetail(id) {
    const modal   = document.getElementById('admin-detail-modal');
    const loading = document.getElementById('admin-detail-loading');
    const body    = document.getElementById('admin-detail-body');

    // Tampilkan modal dalam kondisi loading dulu
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    body.classList.add('hidden');

    fetch(`/admin/data-admin/${id}`, {         
        headers: { 'Accept': 'application/json' }
    })
        .then(response => {
            if (!response.ok) throw new Error('Gagal mengambil data');
            return response.json();
        })
        .then(data => {
            document.getElementById('admin-detail-avatar').textContent =
                data.name ? data.name.substring(0, 2).toUpperCase() : '??';
            document.getElementById('admin-detail-name').textContent = data.name ?? '-';
            document.getElementById('admin-detail-email').textContent = data.email ?? '-';
            document.getElementById('admin-detail-created').textContent = data.created_at ?? '-';

            loading.classList.add('hidden');
            body.classList.remove('hidden');
        })
        .catch(() => {
            loading.textContent = 'Gagal memuat data admin.';
        });
}

function closeAdminDetail() {
    document.getElementById('admin-detail-modal').classList.add('hidden');
}
</script>
@endpush
@endsection