@extends('admin.layout')

@section('title', 'Data Pengguna')
@section('page-title', 'Manajemen Data Pengguna')
@section('search-placeholder', 'Cari pengguna...')

@push('styles')
<style>
.modal { animation: modalPop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.85) translateY(30px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
@endpush

@section('content')

<div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">

    {{-- HEADER --}}
    <div class="p-6 border-b border-rose-50 flex justify-between items-center">
        <div>
            <h3 class="font-bold text-slate-800">Daftar Pengguna</h3>
            <p class="text-xs text-slate-400">Data pengguna sistem</p>
        </div>

        <form method="GET" action="{{ route('admin.pengguna.index') }}" class="flex gap-3">
            <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 border border-slate-200 rounded-xl text-sm">
                <option value="">Semua Status</option>
                <option value="Aktif"    {{ request('status') == 'Aktif'    ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-50">
            @forelse($pageUsers ?? [] as $u)
                @php
                    $id     = $u['user_id']     ?? $u['id_user'] ?? null;
                    $nama   = $u['nama_lengkap'] ?? 'User';
                    $email  = $u['email']        ?? '-';
                    $status = ucfirst(strtolower($u['status'] ?? 'aktif'));
                    $statusCls = strtolower($status) === 'aktif'
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        : 'bg-rose-50 text-rose-700 border-rose-200';
                @endphp
                <tr class="hover:bg-rose-50/10 transition-colors">

                    {{-- Avatar --}}
                    <td class="px-6 py-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-pink-500
                                    flex items-center justify-center text-white font-bold text-xs">
                            {{ strtoupper(substr($nama, 0, 2)) }}
                        </div>
                    </td>

                    {{-- Nama + ID --}}
                    <td class="px-6 py-4">
                        <p class="font-semibold text-slate-800">{{ $nama }}</p>
                        <p class="text-xs text-slate-400">ID: {{ $id ?? '-' }}</p>
                    </td>

                    {{-- Email --}}
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $email }}</td>

                    {{-- Status --}}
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 text-xs rounded-full border {{ $statusCls }}">
                            {{ $status }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 text-center">
                        <button onclick="showUserDetail('{{ $id }}')"
                                class="p-2 hover:bg-slate-100 rounded-lg inline-block text-rose-500"
                                title="Lihat Detail">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-slate-400">
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
            Menampilkan {{ ($currentPage - 1) * 10 + 1 }}–{{ min($currentPage * 10, $total) }}
            dari {{ $total }} data
        </p>
        <div class="flex gap-2">
            @if($currentPage > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                   class="px-4 py-2 border rounded-xl text-sm hover:bg-slate-50">← Prev</a>
            @endif
            @if($currentPage < $totalPages)
                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                   class="px-4 py-2 border rounded-xl text-sm hover:bg-slate-50">Next →</a>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ==================== MODAL DETAIL ==================== --}}
<div id="userModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl max-w-2xl w-full mx-4 overflow-hidden modal">

        {{-- Modal Header --}}
        <div class="p-6 border-b flex justify-between items-center bg-rose-50 rounded-t-3xl">
            <h3 class="text-xl font-bold text-slate-800" id="modalNama">Detail Pengguna</h3>
            <button onclick="closeModal()"
                    class="text-3xl leading-none text-slate-400 hover:text-slate-600">×</button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6" id="modalContent">
            <!-- diisi JS -->
        </div>
    </div>
</div>

<script>
async function showUserDetail(id) {
    const modal   = document.getElementById('userModal');
    const content = document.getElementById('modalContent');

    content.innerHTML = `
        <div class="flex justify-center items-center py-16">
            <span class="material-symbols-outlined animate-spin text-5xl text-rose-400">refresh</span>
        </div>`;
    modal.classList.remove('hidden');

    try {
        const res  = await fetch(`/admin/pengguna/${id}`);
        const user = await res.json();

        if (user.error) {
            content.innerHTML = `<p class="text-red-500 text-center py-10">${user.error}</p>`;
            return;
        }

        document.getElementById('modalNama').textContent =
            user.nama_lengkap || 'Detail Pengguna';

        const val = v => (v !== undefined && v !== null && v !== '') ? v : '-';
        const yn  = v => v == 1 ? 'Ya' : 'Tidak';

        const uid    = val(user.user_id || user.id_user);
        const status = user.status ?? 'aktif';
        const statusCls = status.toLowerCase() === 'aktif'
            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
            : 'bg-rose-50 text-rose-700 border border-rose-200';

        content.innerHTML = `
            <div class="grid grid-cols-2 gap-4">

                {{-- Baris 1: user_id & nama_lengkap --}}
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-400 mb-1">ID User</p>
                    <p class="font-semibold text-slate-800">${uid}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-400 mb-1">Nama Lengkap</p>
                    <p class="font-semibold text-slate-800">${val(user.nama_lengkap)}</p>
                </div>

                {{-- Baris 2: email & status --}}
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-400 mb-1">Email</p>
                    <p class="font-semibold text-slate-800 break-all">${val(user.email)}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-400 mb-1">Status</p>
                    <span class="px-3 py-1 text-xs rounded-full ${statusCls}">
                        ${status.charAt(0).toUpperCase() + status.slice(1).toLowerCase()}
                    </span>
                </div>

                {{-- Baris 3: age & bmi --}}
                <div class="bg-rose-50/40 rounded-xl p-4">
                    <p class="text-xs text-slate-400 mb-1">Usia</p>
                    <p class="font-semibold text-slate-800">${val(user.age)} tahun</p>
                </div>
                <div class="bg-rose-50/40 rounded-xl p-4">
                    <p class="text-xs text-slate-400 mb-1">BMI</p>
                    <p class="font-semibold text-slate-800">${val(user.bmi)}</p>
                </div>

                {{-- Baris 4: pcos_diagnosed & birth_control_use --}}
                <div class="bg-rose-50/40 rounded-xl p-4">
                    <p class="text-xs text-slate-400 mb-1">PCOS Diagnosed</p>
                    <p class="font-semibold text-slate-800">${yn(user.pcos_diagnosed)}</p>
                </div>
                <div class="bg-rose-50/40 rounded-xl p-4">
                    <p class="text-xs text-slate-400 mb-1">Alat Kontrasepsi</p>
                    <p class="font-semibold text-slate-800">${yn(user.birth_control_use)}</p>
                </div>

            </div>`;

    } catch (e) {
        content.innerHTML = `<p class="text-red-500 text-center py-10">Gagal memuat detail pengguna</p>`;
        console.error(e);
    }
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}
</script>

@endsection
