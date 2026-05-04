@extends('admin.layout')

@section('title', 'Detail Pengguna')
@section('page-title', 'Detail Pengguna')

@section('content')

<div class="max-w-4xl mx-auto">
    <div class="bg-white p-8 rounded-3xl border border-rose-100 shadow-sm">

        <div class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-2xl font-bold">{{ $user['nama_lengkap'] ?? 'User' }}</h2>
                <p class="text-slate-500">ID: {{ $user['id_user'] ?? '-' }}</p>
            </div>
            <span class="px-4 py-2 text-sm rounded-2xl
                {{ strtolower($user['status'] ?? '') === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ $user['status'] ?? 'Aktif' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            <!-- Informasi Dasar -->
            <div>
                <h3 class="font-semibold text-slate-700 mb-5 border-b pb-2">Informasi Dasar</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-slate-500">Email</p>
                        <p class="font-medium">{{ $user['email'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">No. Telepon</p>
                        <p class="font-medium">{{ $user['no_telepon'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Usia</p>
                        <p class="font-medium">{{ $user['usia'] }} tahun</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Berat Badan</p>
                        <p class="font-medium">{{ $user['berat_badan'] }} kg</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Tinggi Badan</p>
                        <p class="font-medium">{{ $user['tinggi_badan'] }} cm</p>
                    </div>
                    <div>
                        <p class="text-slate-500">BMI</p>
                        <p class="font-medium">{{ $user['bmi'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Kesehatan & Gaya Hidup -->
            <div>
                <h3 class="font-semibold text-slate-700 mb-5 border-b pb-2">Data Kesehatan & Gaya Hidup</h3>
                <div class="space-y-5 text-sm">

                    <div>
                        <p class="text-slate-500">Jam Tidur per Malam</p>
                        <p class="font-medium">{{ $user['sleep_hours'] }} jam</p>
                        <p class="text-xs text-slate-400">Rata-rata jam tidur dalam sehari</p>
                    </div>

                    <div>
                        <p class="text-slate-500">Frekuensi Olahraga</p>
                        <p class="font-medium">{{ $user['exercise_frequency'] }} kali/minggu</p>
                        <p class="text-xs text-slate-400">Berapa kali berolahraga dalam seminggu</p>
                    </div>

                    <div>
                        <p class="text-slate-500">Skor Stres Baseline</p>
                        <p class="font-medium">{{ $user['stress_score_baseline'] }}</p>
                        <p class="text-xs text-slate-400">Skor stres awal (semakin tinggi semakin stres)</p>
                    </div>

                    <div>
                        <p class="text-slate-500">Kualitas Diet</p>
                        <p class="font-medium">{{ $user['diet_quality'] }}/10</p>
                        <p class="text-xs text-slate-400">Penilaian kualitas pola makan</p>
                    </div>

                    <div>
                        <p class="text-slate-500">Asupan Air</p>
                        <p class="font-medium">{{ $user['water_intake_l'] }} liter/hari</p>
                    </div>

                    <div>
                        <p class="text-slate-500">Konsumsi Kafein</p>
                        <p class="font-medium">{{ $user['caffeine_cups_day'] }} cangkir/hari</p>
                    </div>

                    <div>
                        <p class="text-slate-500">PCOS Diagnosed</p>
                        <p class="font-medium">{{ $user['pcos_diagnosed'] == 1 ? 'Ya' : 'Tidak' }}</p>
                        <p class="text-xs text-slate-400">Polycystic Ovary Syndrome</p>
                    </div>

                    <div>
                        <p class="text-slate-500">Penggunaan Alat Kontrasepsi</p>
                        <p class="font-medium">{{ $user['birth_control_use'] == 1 ? 'Ya' : 'Tidak' }}</p>
                    </div>

                    <div>
                        <p class="text-slate-500">Merokok</p>
                        <p class="font-medium">{{ $user['smoking_status'] == 1 ? 'Ya' : 'Tidak' }}</p>
                    </div>

                    <div>
                        <p class="text-slate-500">Konsumsi Alkohol</p>
                        <p class="font-medium">{{ $user['alcohol_consumption'] == 1 ? 'Ya' : 'Tidak' }}</p>
                    </div>

                </div>
            </div>

        </div>

        <div class="mt-10 pt-6 border-t">
            <a href="{{ route('admin.pengguna') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 rounded-2xl text-sm font-medium transition">
                ← Kembali ke Daftar Pengguna
            </a>
        </div>

    </div>
</div>

@endsection
