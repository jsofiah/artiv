@extends('layouts.customer')

@section('title', 'Pesanan Saya')

@section('content')
<div class="py-8">

    {{-- Judul --}}
    <h1 class="text-4xl font-bold text-ink">Pesanan Saya</h1>
    <p class="text-muted mt-1">
        Pantau perkembangan pengerjaan proyek desain aktif dan riwayat transaksi kreatif Anda.
    </p>
    <div class="mt-3 h-1 w-24 rounded-full bg-primary"></div>

    {{-- Tab --}}
    <div class="flex gap-6 border-b border-line mt-6 mb-5">
        @foreach (['aktif' => ['Pesanan Aktif', $countAktif], 'riwayat' => ['Riwayat Selesai', $countRiwayat]] as $key => [$label, $count])
            <a href="{{ route('customer.pesanan', ['tab' => $key]) }}"
               class="pb-3 text-sm font-semibold flex items-center gap-2
                      {{ $tab === $key ? 'text-primary border-b-2 border-primary' : 'text-muted' }}">
                {{ $label }}
                <span class="rounded-full bg-line px-2 text-xs text-primary">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    {{-- Filter bar --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <input type="text" placeholder="Cari nomor pesanan, nama jasa, atau desainer..."
               class="flex-1 min-w-[240px] rounded-full border border-line bg-white px-4 py-2 text-sm
                      placeholder:text-neutral focus:outline-none focus:ring-2 focus:ring-primary">
        <select class="rounded-full border border-line bg-white pl-4 pr-10 py-2 text-xs font-medium text-ink
                       appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:12px]">
            <option>Status: Semua Status</option>
        </select>
        <select class="rounded-full border border-line bg-white pl-4 pr-10 py-2 text-xs font-medium text-ink
                       appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:12px]">
            <option>Kategori: Semua Kategori</option>
        </select>
        <select class="rounded-full border border-line bg-white pl-4 pr-10 py-2 text-xs font-medium text-ink
                       appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:12px]">
            <option>Urutkan: Deadline Terdekat</option>
        </select>
    </div>

    @php
    use App\Helpers\StorageHelper;
@endphp

{{-- Daftar pesanan --}}
@forelse ($orders as $order)
    @php
        $p = $order->progress;
        $thumbUrl = StorageHelper::url($order->product->thumbnail_url, 'media');
    @endphp
    <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 border border-transparent">
        <div class="flex items-center gap-5">

            <div class="w-28 h-28 shrink-0 rounded-xl bg-line/60 overflow-hidden flex items-center justify-center
                        text-[10px] font-semibold text-muted uppercase">
                @if ($thumbUrl)
                    <img src="{{ $thumbUrl }}" alt="{{ $order->product->name }}" class="w-full h-full object-cover">
                @else
                    Preview Jasa
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <span class="inline-block rounded-full bg-primary px-3 py-0.5 text-[11px] font-semibold text-white">
                    Tahap {{ $p['step'] }}: {{ $p['label'] }}
                </span>

                <h2 class="text-lg font-bold text-ink mt-1 truncate">{{ $order->product->name }}</h2>

                <div class="flex flex-wrap items-center gap-x-4 text-xs text-muted mt-1">
                    <span>Kreator: <b class="text-ink">{{ $order->designer->full_name ?? 'Belum ditentukan' }}</b></span>
                    <span>Estimasi:
                        <b class="text-ink">{{ $order->deadline?->translatedFormat('d M Y') ?? '-' }}</b>
                    </span>
                </div>

                <p class="text-xs text-neutral mt-1">
                    {{ $order->productTier->name }} • {{ $order->order_code }}
                </p>

                @if ($tab === 'aktif')
                    <div class="mt-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-semibold text-ink">Progres Pengerjaan: Tahap {{ $p['step'] }} dari 3</span>
                            <span class="font-semibold text-primary">{{ $p['percent'] }}% Selesai</span>
                        </div>
                        <div class="h-2 rounded-full bg-line overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-primary to-lime"
                                 style="width: {{ $p['percent'] }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            <a href="{{ route('customer.pesanan.show', $order->id) }}"
               class="shrink-0 rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white
                      hover:bg-primary-dark transition">
                Lihat Pesanan →
            </a>
        </div>
    </div>
@empty
        <div class="bg-white rounded-2xl p-12 shadow-sm text-center border-2 border-dashed border-line">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-line/50 flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-primary" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>

            <h2 class="text-lg font-bold text-ink">
                {{ $tab === 'riwayat' ? 'Belum Ada Riwayat Pesanan' : 'Belum Ada Pesanan Aktif' }}
            </h2>
            <p class="text-sm text-muted mt-2 max-w-md mx-auto">
                @if ($tab === 'riwayat')
                    Pesanan yang sudah selesai atau dibatalkan akan muncul di sini.
                @else
                    Anda belum memiliki pesanan desain yang sedang berjalan. Temukan kreator terbaik dan mulai proyek impian Anda sekarang!
                @endif
            </p>

            @if ($tab === 'aktif')
                <a href="{{ route('customer.katalog') }}"
                   class="inline-flex items-center gap-2 mt-6 rounded-full bg-lime px-6 py-3 text-sm font-bold text-ink hover:brightness-95 transition">
                    Jelajahi Katalog Jasa Sekarang →
                </a>
            @endif
        </div>
    @endforelse
</div>
@endsection