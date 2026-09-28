@extends('layouts.customer')

@section('title', 'Pesanan Saya')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Pesanan Saya</h1>

    @if ($orders->isEmpty())
        <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
            <p class="text-gray-500">Belum ada pesanan.</p>
            <a href="{{ route('customer.katalog') }}"
               class="inline-block mt-4 text-[#745BB8] font-medium hover:underline">
                Jelajahi katalog jasa →
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('customer.pesanan.show', $order->id) }}"
                   class="block bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:border-[#745BB8] transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $order->product->name }}</p>
                            <p class="text-sm text-slate-500">
                                {{ $order->productTier->name }} • {{ $order->order_code }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-[#6D28D9]">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-slate-400 capitalize">{{ $order->status }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection