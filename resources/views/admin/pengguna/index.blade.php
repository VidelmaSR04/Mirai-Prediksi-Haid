@extends('admin.layout')

@section('title', 'Data Pengguna')
@section('page-title', 'Manajemen Data Pengguna')
@section('search-placeholder', 'Cari pengguna...')

@push('styles')
<style>
.modal {
    animation: modalPop 0.3s ease;
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
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
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-slate-200 rounded-xl text-sm">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
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
                    <th class="px-6 py-4">Telepon</th>
                    <th class="px-6 py-4 text-center">Usia</th>
                    <th class="px-6 py-4 text-center">BMI</th>
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
                    $usia   = $u['usia'] ?? '-';
                    $bmi    = $u['bmi'] ?? '-';
                    $status = $u['status'] ?? 'Aktif';
                    $statusCls = strtolower($status) === 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700';
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
                    <td class="px-6 py-4 text-center">{{ $usia }} thn</td>
                    <td class="px-6 py-4 text-center font-medium">{{ $bmi }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 text-xs rounded-full border {{ $statusCls }}">{{ $status }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{-- FIX: id_user adalah string, pakai tanda kutip --}}
                        <button onclick="showUserDetail('{{ $id }}')"
                                class="p-2 hover:bg-slate-100 rounded-lg inline-block text-rose-500">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-10 text-slate-400">Tidak ada data pengguna</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($totalPages > 1)
    <div class="px-6 py-4 border-t border-rose-50 flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Menampilkan {{ ($currentPage-1)*10 + 1 }} - {{ min($currentPage*10, $total) }} dari {{ $total }} data
        </p>
        <div class="flex gap-2">
            @if($currentPage > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage-1]) }}" class="px-4 py-2 border rounded-xl text-sm hover:bg-slate-50">← Prev</a>
            @endif
            @if($currentPage < $totalPages)
                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage+1]) }}" class="px-4 py-2 border rounded-xl text-sm hover:bg-slate-50">Next →</a>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ==================== MODAL DETAIL ==================== --}}
<div id="userModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl max-w-3xl w-full mx-4 max-h-[92vh] overflow-hidden modal">
        <div class="p-6 border-b flex justify-between items-center bg-rose-50 rounded-t-3xl">
            <h3 class="text-xl font-bold" id="modalNama">Detail Pengguna</h3>
            <button onclick="closeModal()" class="text-3xl leading-none text-slate-400 hover:text-slate-600">×</button>
        </div>
        <div class="p-6 overflow-auto max-h-[75vh]" id="modalContent">
            <!-- Content loaded by JS -->
        </div>
    </div>
</div>

<script>
async function showUserDetail(id) {
    const modal   = document.getElementById('userModal');
    const content = document.getElementById('modalContent');

    content.innerHTML = `
        <div class="flex justify-center items-center py-20">
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

        document.getElementById('modalNama').textContent = user.nama_lengkap || 'Detail Pengguna';

        // Helper: tampilkan nilai atau '-' jika kosong/null/undefined
        const val = (v) => (v !== undefined && v !== null && v !== '') ? v : '-';

        content.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Informasi Dasar -->
                <div>
                    <h4 class="font-semibold text-slate-700 mb-4 border-b pb-2">Informasi Dasar</h4>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-slate-500">ID User</p>
                            <p class="font-medium">${val(user.id_user)}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Email</p>
                            <p class="font-medium">${val(user.email)}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">No. Telepon</p>
                            <p class="font-medium">${val(user.no_telepon)}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Usia</p>
                            <p class="font-medium">${val(user.age)} tahun</p>
                        </div>
                        <div>
                            <p class="text-slate-500">BMI</p>
                            <p class="font-medium">${val(user.bmi)}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Status</p>
                            <span class="px-3 py-1 text-xs rounded-full border ${user.status?.toLowerCase() === 'aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'}">
                                ${val(user.status)}
                            </span>
                        </div>
                        <div>
                            <p class="text-slate-500">Domisili</p>
                            <p class="font-medium">${val(user.state)}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Bergabung</p>
                            <p class="font-medium">${val(user.created_at)}</p>
                        </div>
                    </div>
                </div>

                <!-- Data Kesehatan & Gaya Hidup -->
                <div>
                    <h4 class="font-semibold text-slate-700 mb-4 border-b pb-2">Data Kesehatan & Gaya Hidup</h4>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-slate-500">Jam Tidur per Malam</p>
                            <p class="font-medium">${val(user.sleep_hours)} jam</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Frekuensi Olahraga</p>
                            <p class="font-medium">${val(user.exercise_frequency)}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Skor Stres</p>
                            <p class="font-medium">${val(user.stress_score_baseline)}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Kualitas Diet</p>
                            <p class="font-medium">${val(user.diet_quality)}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Asupan Air</p>
                            <p class="font-medium">${val(user.water_intake_liters)} liter/hari</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Konsumsi Kafein</p>
                            <p class="font-medium">${val(user.caffeine_intake)} cangkir/hari</p>
                        </div>
                        <div>
                            <p class="text-slate-500">PCOS</p>
                            <p class="font-medium">${user.pcos_diagnosed == 1 ? 'Ya' : 'Tidak'}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Alat Kontrasepsi</p>
                            <p class="font-medium">${user.birth_control_use == 1 ? 'Ya' : 'Tidak'}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Merokok</p>
                            <p class="font-medium">${val(user.smoking_status)}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Konsumsi Alkohol</p>
                            <p class="font-medium">${val(user.alcohol_consumption)}</p>
                        </div>
                    </div>
                </div>

            </div>
        `;
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
