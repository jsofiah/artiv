<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    //
    public function show(string $order)
    {
        // Ambil order milik customer yang sedang login beserta relasi pendukungnya
        $order = Order::with([
            'product',
            'productTier',
            'expressFee',
            'payment',
        ])
            ->where('customer_id', Auth::id())
            ->findOrFail($order);

        // Cek jika pesanan sudah dibayar
        if ($order->payment && in_array($order->payment->status, ['paid', 'verified'])) {
            return redirect()
                ->route('customer.pesanan.show', $order->id)
                ->with('info', 'Pesanan ini sudah lunas.');
        }

        return view('customer.pembayaran', compact('order'));
    }

    /**
     * Memproses konfirmasi pembayaran (termasuk upload bukti foto)
     */
    public function store(Request $request, string $order)
    {
        Log::info('=== PembayaranController@store dipanggil ===', ['order_id' => $order]);

        // 1. Validasi input
        $request->validate([
            'method'    => 'required|string|max:50',
            'proof'     => 'required|file|max:5120|mimes:png,jpg,jpeg,webp,pdf', // Maks 5MB
        ]);

        // 2. Memastikan pesanan dari customer yang sedang login
        $orderModel = Order::where('customer_id', Auth::id())->findOrFail($order);

        try {
            DB::beginTransaction();

            // 3. Upload bukti bayar
            $proofPath = null;
            if ($request->hasFile('proof')) {
                $storage = app(SupabaseStorageService::class);
                $proofPath = $storage->upload($request->file('proof'), 'payments'); 
            }

            // 4. Hitung total tagihan
            $totalAmount = $orderModel->total_amount ?? $orderModel->total_price ?? 0;

            // 5. Simpan / perbarui record Payment
            $payment = Payment::updateOrCreate(
                ['order_id' => $orderModel->id],
                [
                    'method'    => $request->method,
                    'amount'    => $totalAmount,
                    'proof_url' => $proofPath,
                    'status'    => 'pending',
                    'paid_at'   => now(),
                ]
            );

            // 6. Update status pesanan jika diperlukan
            // $orderModel->update(['status' => 'menunggu_verifikasi_pembayaran']);

            DB::commit();

            return redirect()
                ->route('customer.pesanan.show', $orderModel->id)
                ->with('status', 'Bukti pembayaran berhasil diunggah. Mohon tunggu verifikasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memproses pembayaran: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.']);
        }
    }
}
