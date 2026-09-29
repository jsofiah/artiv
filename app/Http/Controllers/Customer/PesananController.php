<?php

namespace App\Http\Controllers\Customer;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Helpers\StorageHelper;
use App\Models\OrderReference;
use App\Models\MessageAttachment;
use Illuminate\Support\Facades\Http;
use App\Services\SupabaseStorageService;
use Illuminate\Support\Facades\Log;


class PesananController extends Controller
{
       /* Daftar semua pesanan milik customer yang login. */
public function index(Request $request): View
{
    $tab = $request->query('tab', 'aktif');
    // GANTI kalau ada nilai status lain untuk pesanan selesai/dibatalkan
    $selesai = ['completed', 'cancelled'];

    $base = Order::with(['product', 'productTier', 'designer'])
        ->where('customer_id', Auth::id());

    $countAktif   = (clone $base)->whereNotIn('status', $selesai)->count();
    $countRiwayat = (clone $base)->whereIn('status', $selesai)->count();

    $orders = $tab === 'riwayat'
        ? (clone $base)->whereIn('status', $selesai)->latest()->get()
        : (clone $base)->whereNotIn('status', $selesai)->orderByDesc('created_at')->get();

    return view('customer.pesanan', compact('orders', 'tab', 'countAktif', 'countRiwayat'));
}

    /* Detail pesanan + chat. */
    public function show(string $order): View
    {
        $order = Order::with([
            'customer',
            'designer.designerStats',
            'product',
            'productTier',
            'expressFee',
            'references',
            'conversation.messages.sender',
            'conversation.messages.attachments',
            'conversation.messages.deliverable',
            'logs.actor',
            'deliverables',
            'payment',
            'review',
        ])
            ->where('customer_id', Auth::id())
            ->findOrFail($order);

        return view('customer.pesanan.detail', compact('order'));
    }

    /* Kirim pesan dari customer. */
    public function kirimPesan(Request $request, string $order)
    {
        Log::info('=== kirimPesan dipanggil ===');
        Log::info('Request data:', $request->all());
        Log::info('Has file:', ['has' => $request->hasFile('attachment')]);
        Log::info('Validasi lolos');
        
        $request->validate([
            'isi' => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|max:25600|mimes:png,jpg,jpeg,gif,webp,pdf,zip,doc,docx',
        ]);

        Log::info('Validasi lolos');


        if (!$request->filled('isi') && !$request->hasFile('attachment')) {
            return back()->withErrors(['isi' => 'Pesan atau lampiran harus diisi.']);
        }

        $orderModel = Order::where('customer_id', Auth::id())->findOrFail($order);

        $conversation = $orderModel->conversation;

        // Kalau belum ada conversation, dan order sudah punya designer, buat baru
        if (!$conversation && $orderModel->designer_id) {
            $conversation = Conversation::create([
                'order_id'    => $orderModel->id,
                'customer_id' => $orderModel->customer_id,
                'designer_id' => $orderModel->designer_id,
                'status'      => 'active',
            ]);
        }

        abort_if(!$conversation, 404, 'Percakapan belum tersedia. Menunggu designer.');

        $messageType = $request->hasFile('attachment') ? 'file' : 'text';

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'type'            => $messageType,
            'body'            => $request->isi,
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $storage = app(SupabaseStorageService::class);
            $path = $storage->upload($file, 'attachments');

            MessageAttachment::create([
                'message_id' => $message->id,
                'file_url'   => $path,
                'file_name'  => $file->getClientOriginalName(),
                'file_size'  => $file->getSize(),
                'mime_type'  => $file->getMimeType(),
                'file_type'  => $file->getClientOriginalExtension(),
            ]);
        }

        broadcast(new MessageSent($message, Auth::user()))->toOthers();

        $conversation->touch();

        return redirect()
            ->route('customer.pesanan.show', $orderModel->id)
            ->with('status', 'Pesan terkirim.');
    }
    

    public function downloadReference(string $order, string $reference)
    {
        // Pastikan order milik customer yang login
        $orderModel = Order::where('customer_id', Auth::id())->findOrFail($order);

        $ref = OrderReference::where('order_id', $orderModel->id)
            ->where('id', $reference)
            ->firstOrFail();

        if ($ref->type !== 'file' || !$ref->file_url) {
            abort(404, 'File tidak ditemukan.');
        }

        return $this->streamFromSupabase(
            StorageHelper::url($ref->file_url),
            $ref->file_name ?? 'file'
        );
    }

    public function downloadAttachment(string $order, string $attachment)
    {
        $orderModel = Order::where('customer_id', Auth::id())->findOrFail($order);

        $att = MessageAttachment::whereHas('message.conversation', function ($q) use ($orderModel) {
                $q->where('order_id', $orderModel->id);
            })
            ->where('id', $attachment)
            ->firstOrFail();

        return $this->streamFromSupabase(
            StorageHelper::url($att->file_url),
            $att->file_name ?? 'file'
        );
    }

    /* Stream file dari Supabase sebagai download. */
    private function streamFromSupabase(string $url, string $filename)
    {
        $response = Http::withOptions(['stream' => true])->get($url);

        if ($response->failed()) {
            abort(404, 'Gagal mengambil file.');
        }

        return response()->streamDownload(function () use ($response) {
            echo $response->body();
        }, $filename);
    }
}