@extends('admin.layout')

@section('page-title', 'Detail Admin')

@section('content')
<div class="p-6">
    <a href="{{ route('admin.data-admin.index') }}" class="text-pink-500 text-sm mb-4 inline-block">
        &larr; Kembali ke Daftar Admin
    </a>

    <div class="bg-white rounded-xl shadow-sm p-8 max-w-xl">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-pink-500 text-white flex items-center
                        justify-center text-xl font-semibold">
                {{ strtoupper(substr($admin->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $admin->name }}</h2>
                <p class="text-sm text-gray-400">Administrator</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-400 text-xs">EMAIL</p>
                <p class="text-gray-800">{{ $admin->email }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-xs">DIBUAT PADA</p>
                <p class="text-gray-800">
                    {{ \Carbon\Carbon::parse($admin->created_at)->format('d M Y, H:i') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection