@extends('admin.layout')

@section('title', 'Detail Pengguna')
@section('page-title', 'Detail Pengguna')

@section('content')

@php
    $nama  = $user['nama_lengkap'] ?? 'User';
    $email = $user['email'] ?? '-';
    $telp  = $user['no_telepon'] ?? '-';
    $usia  = $user['usia'] ?? null;
    $bb    = $user['berat_badan'] ?? null;
    $tb    = $user['tinggi_badan'] ?? null;
    $status= $user['status'] ?? 'Aktif';
@endphp

<div class="max-w-3xl mx-auto">

    <div class="bg-white p-6 rounded-2xl border border-rose-100 shadow-sm">

        <h2 class="text-xl font-bold mb-4">{{ $nama }}</h2>

        <div class="space-y-2 text-sm text-slate-600">

            <p><b>Email:</b> {{ $email }}</p>
            <p><b>No HP:</b> {{ $telp }}</p>
            <p><b>Usia:</b> {{ $usia ? $usia.' tahun' : '-' }}</p>
            <p><b>Berat:</b> {{ $bb ? $bb.' kg' : '-' }}</p>
            <p><b>Tinggi:</b> {{ $tb ? $tb.' cm' : '-' }}</p>
            <p><b>Status:</b> {{ $status }}</p>

        </div>

        <div class="mt-5 flex gap-3">
            <a href="{{ route('admin.pengguna') }}"
               class="px-4 py-2 border rounded-xl text-sm">
               Kembali
            </a>
        </div>

    </div>

</div>

@endsection
