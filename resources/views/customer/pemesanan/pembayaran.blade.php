@extends('layouts.customer')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <span class="inline-block text-xs font-bold tracking-wider text-[#745BB8] uppercase bg-purple-50 px-3 py-1 rounded-full mb-2">
            TAHAP 3 DARI 3 • PEMBAYARAN PESANAN
        </span>
        <h1 class="text-3xl font-bold text-slate-900">Konfirmasi Pembayaran</h1>
        <p class="text-slate-500 mt-1">Pastika pesanan anda sudah sesuai dan lakukan pembayaran.</p>
    </div>

    <!-- Alert Error jika ada -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Utama -->
    <form action="{{ route('customer.pesanan.pembayaran.store', $order->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="method" value="qris">

        <!-- 1. Informasi Pembayaran -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center font-bold text-sm shrink-0">
                    1
                </div>
                <h2 class="text-xl font-bold text-slate-900">Informasi Pembayaran</h2>
            </div>

            <!-- Layout: kiri barcode, Kanan info -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Template Barcode (dummy aja) -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 inline-block">
                        <svg class="w-56 h-56 text-slate-900" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14-2h4v2h-4v-2zm-4 4h2v2h-2v-2zm4 0h4v4h-4v-4zm-4 4h2v2h-2v-2zm2-8h2v2h-2v-2zm-6 2h2v2H8v-2zm2 2h2v2h-2v-2z"/>
                        </svg>
                    </div>
                </div>

                <!--Total Nominal, Merchant, Batas Waktu, & Langkah Pembayaran -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- Total Nominal & Merchant Resmi -->
                    <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 tracking-wider">TOTAL NOMINAL:</p>
                            <p class="text-2xl font-extrabold text-[#6D28D9] mt-0.5">
                                Rp {{ number_format($order->total_amount ?? $order->total_price ?? 650000, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] font-bold text-slate-400 tracking-wider">MERCHANT RESMI:</p>
                            <p class="text-sm font-bold text-slate-900 mt-1">ARTIV Official</p>
                        </div>
                    </div>

                    <!-- Batas Waktu Bayar -->
                    <div class="bg-amber-50/80 border border-amber-200/60 rounded-xl px-4 py-3 flex items-center justify-between text-amber-900 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">Batas Waktu Bayar:</span>
                        </div>
                        <span class="font-bold bg-white px-3 py-1 rounded-lg border border-amber-200">23 Jam 59 Menit</span>
                    </div>

                    <!-- Langkah Pembayaran -->
                    <div class="pt-1">
                        <p class="font-bold text-xs text-slate-900 tracking-wider mb-2">LANGKAH PEMBAYARAN QRIS:</p>
                        <ol class="list-decimal list-inside space-y-1.5 text-xs md:text-sm text-slate-600">
                            <li>Pastikan paket, harga, dan total pembayaran sudah sesuai.</li>
                            <li>Pilih menu <b>Scan / Bayar</b> dan arahkan kamera ke kode QR di samping.</li>
                            <li>Pastikan nama merchant tertera <b class="text-slate-900">ARTIV Official</b>.</li>
                            <li>Ikuti instruksi pembayaran hingga transaksi berhasil diproses.</li>
                            <li>Setelah pembayaran berhasil dikonfirmasi, pesanan akan masuk ke sistem dan tunggu desainer mengambil order.</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. Upload Bukti Pembayaran -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 mb-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center font-bold text-sm shrink-0">
                    2
                </div>
                <h2 class="text-xl font-bold text-slate-900">Upload Bukti Pembayaran</h2>
            </div>

            <!-- Drag & Drop File Upload Area -->
            <div class="relative border-2 border-dashed border-slate-300 hover:border-[#745BB8] rounded-2xl p-10 text-center transition bg-slate-50/30 group cursor-pointer mb-4">
                <input type="file" name="proof" id="proof" accept=".png, .jpg, .jpeg, .webp, .pdf" required
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                    onchange="updateFileName(this)">

                <div class="flex flex-col items-center justify-center space-y-3 pointer-events-none">
                    <div class="w-14 h-14 rounded-full bg-purple-50 text-[#745BB8] flex items-center justify-center group-hover:scale-110 transition duration-200">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-slate-800" id="file-label">Unggah Struk atau Bukti Transfer</p>
                        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                            Seret dan lepaskan tangkapan layar m-banking, resi ATM, atau berkas bukti di sini. Format yang didukung: JPG, PNG, PDF (Maksimal 10MB).
                        </p>
                    </div>
                </div>
            </div>

            <!-- Catatan Verifikasi -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-3 flex items-center gap-3 text-slate-700 text-sm">
                <svg class="w-5 h-5 text-[#745BB8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><b>Verifikasi Cepat:</b> Notifikasi konfirmasi akan langsung dikirim ke menu <i>Pesanan Saya</i></span>
            </div>
        </div>

        <!-- Alur Langkah Selanjutnya -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-8">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">ALUR LANGKAH SELANJUTNYA:</p>
            <div class="space-y-2.5">
                <div class="flex items-center gap-3 text-sm text-slate-700">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold">✓</div>
                    <span>Pengisian Formulir</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-slate-700">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold">✓</div>
                    <span>Konfirmasi Ringkasan</span>
                </div>
                <div class="flex items-center gap-3 text-sm font-semibold text-[#745BB8] bg-purple-100/60 p-2.5 rounded-xl border border-purple-200/60">
                    <div class="w-6 h-6 rounded-full bg-[#745BB8] text-white flex items-center justify-center text-xs font-bold">3</div>
                    <span>Pembayaran Pesanan</span>
                </div>
            </div>
        </div>

        <!-- Tombol Kirim -->
        <div class="flex justify-end">
            <button type="submit" class="bg-[#CCFF00] hover:bg-[#b8e600] text-slate-900 font-bold px-8 py-3.5 rounded-full shadow-md transition duration-200 flex items-center gap-2 cursor-pointer">
                <span>Kirim</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>
    </form>
</div>

<script>
    function updateFileName(input) {
        const label = document.getElementById('file-label');
        if (input.files && input.files[0]) {
            label.textContent = "File dipilih: " + input.files[0].name;
            label.classList.add('text-[#745BB8]', 'font-bold');
        }
    }
</script>
@endsection