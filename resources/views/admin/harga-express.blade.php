@extends('layouts.admin')

@section('title', 'Harga Express')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">
        Halo, {{ auth()->user()->full_name }}! 👋
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Pekerjaan Aktif</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">0</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Selesai Bulan Ini</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">0</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <p class="text-sm text-gray-500">Pendapatan</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">Rp 0</p>
        </div>
    </div>
@endsection