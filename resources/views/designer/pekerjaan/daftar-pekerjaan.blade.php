@extends('layouts.designer')

@section('title', 'Pekerjaan Saya')

@section('content')
<div class="px-8 py-8">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Pekerjaan Saya</h1>
            <p class="text-slate-500 mt-1.5">
                Kelola pesanan yang sedang dan sudah kamu kerjakan sebagai designer.
            </p>
        </div>
        <span class="px-4 py-2 rounded-full bg-violet-50 text-[#6D28D9] text-sm font-semibold shrink-0">
            Total: {{ $totalSemua }}
        </span>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-sm text-slate-500">Total Pekerjaan</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalSemua }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-sm text-slate-500">Dikerjakan</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalDikerjakan }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-sm text-slate-500">Selesai</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalSelesai }}</p>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <form method="GET" action="{{ route('designer.pekerjaan.index') }}"
          class="bg-white rounded-2xl border border-slate-100 shadow-sm p-2.5 pl-4 flex items-center gap-3 mb-6">
        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4-4"/>
        </svg>
        <input
            type="text"
            name="q"
            value="{{ $kataKunci }}"
            placeholder="Cari order code, layanan, atau customer..."
            class="flex-1 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none rounded-lg bg-transparent py-2.5 border-[#6D28D9]/20"
        >
        <div class="flex items-center gap-2 pl-3 border-l border-slate-100">
            <label for="status" class="text-sm text-slate-400 shrink-0 hidden sm:block">Status:</label>
            <select id="status" name="status" onchange="this.form.submit()"
                    class="text-sm font-medium text-slate-700 bg-slate-50 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#6D28D9]/20 border-[#6D28D9]/20 min-w-[140px]">
                <option value="semua"      @selected($status === 'semua')>Semua</option>
                <option value="dikerjakan" @selected($status === 'dikerjakan')>Dikerjakan</option>
                <option value="selesai"    @selected($status === 'selesai')>Selesai</option>
            </select>
        </div>
        <button type="submit"
                class="shrink-0 text-sm font-semibold text-white bg-[#6D28D9] hover:bg-[#5B21B6] px-5 py-2.5 rounded-full transition">
            Cari
        </button>
    </form>

    {{-- Grid --}}
    @if ($pekerjaan->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 py-20 text-center text-slate-400">
            Belum ada pekerjaan yang cocok.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($pekerjaan as $order)
                @php
                    $statusMap = [
                        'pending'          => ['bg-amber-50 text-amber-700',    'Menunggu'],
                        'waiting_designer' => ['bg-amber-50 text-amber-700',    'Mencari Desainer'],
                        'in_progress'      => ['bg-blue-50 text-blue-700',      'Dikerjakan'],
                        'deliverable_sent' => ['bg-violet-50 text-violet-700',  'Review'],
                        'revision_needed'  => ['bg-orange-50 text-orange-700',  'Revisi'],
                        'completed'        => ['bg-emerald-50 text-emerald-700','Selesai'],
                        'cancelled'        => ['bg-red-50 text-red-700',        'Batal'],
                    ];
                    [$statusCls, $statusLabel] = $statusMap[$order->status] ?? ['bg-slate-100 text-slate-600', $order->status];
                @endphp

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">

                    {{-- Thumbnail --}}
                    <div class="relative h-40 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center overflow-hidden">
                        @php $thumbUrl = \App\Helpers\StorageHelper::url($order->product->thumbnail_url); @endphp
                        @if ($thumbUrl)
                            <img src="{{ $thumbUrl }}" alt="{{ $order->product->name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16l5-5a2 2 0 012.8 0L16 16m-2-2l1.6-1.6a2 2 0 012.8 0L21 15M3 6h18v13H3V6z"/>
                            </svg>
                        @endif

                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-md {{ $statusCls }} text-[11px] font-bold tracking-wide">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- Konten --}}
                    <div class="p-4 flex flex-col flex-1">
                        <p class="text-xs font-semibold text-[#6D28D9] mb-1 truncate">
                            {{ $order->order_code }}
                        </p>
                        <h3 class="font-bold text-slate-900 leading-snug mb-2 line-clamp-2">
                            {{ $order->product->name }}
                        </h3>
                        <p class="text-sm text-slate-500 leading-relaxed line-clamp-3 mb-4">
                            {{ $order->customer->full_name ?? 'Customer' }}
                            • {{ $order->productTier->name }}
                            @if ($order->is_express) · Express @endif
                        </p>

                        <div class="mt-auto">
                            <div class="flex items-center gap-2 text-xs text-slate-500 border-t border-slate-100 pt-3">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="17" rx="2"/>
                                    <path stroke-linecap="round" d="M8 2v4M16 2v4M3 9h18"/>
                                </svg>
                                Tenggat:
                                <span class="font-medium text-slate-700">
                                    {{ $order->deadline?->translatedFormat('d M Y') ?? '-' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm mt-2.5 mb-4">
                                <span class="text-slate-500">Biaya:</span>
                                <span class="font-bold text-slate-900">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </span>
                            </div>

                            <a href="{{ route('designer.pekerjaan.show', $order->id) }}"
                               class="w-full inline-flex items-center justify-center gap-1.5 border border-slate-200 hover:bg-[#D5FC55] hover:text-[#1A1A1A] text-slate-700 text-sm font-semibold rounded-full py-2.5 transition">
                                Lihat Pekerjaan
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($pekerjaan->hasPages())
            <div class="mt-8">
                {{ $pekerjaan->links() }}
            </div>
        @endif
    @endif

</div>
@endsection