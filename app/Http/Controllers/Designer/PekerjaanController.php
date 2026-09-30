<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Order;
use App\Models\OrderReference;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class PekerjaanController extends Controller
{
    /**
     * Daftar pekerjaan milik designer yang login.
     */
    public function index(Request $request): View
    {
        $kataKunci = trim((string) $request->query('q', ''));
        $status    = $request->query('status', 'semua');

        $query = Order::with(['product', 'productTier', 'customer'])
            ->where('designer_id', Auth::id());

        // Filter status — 3 kategori
        $statusGroups = [
            'dikerjakan' => [
                'pending',
                'waiting_designer',
                'in_progress',
                'deliverable_sent',
                'revision_needed',
            ],
            'selesai' => ['completed'],
        ];

        if ($status !== 'semua' && isset($statusGroups[$status])) {
            $query->whereIn('status', $statusGroups[$status]);
        }

        // Pencarian
        if ($kataKunci !== '') {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('order_code', 'ilike', "%{$kataKunci}%")
                  ->orWhereHas('product', fn($p) => $p->where('name', 'ilike', "%{$kataKunci}%"))
                  ->orWhereHas('customer', fn($c) => $c->where('full_name', 'ilike', "%{$kataKunci}%"));
            });
        }

        $pekerjaan = $query->orderByDesc('created_at')->paginate(6)->withQueryString();

        // Statistik
        $totalSemua      = Order::where('designer_id', Auth::id())->count();
        $totalDikerjakan = Order::where('designer_id', Auth::id())
            ->whereIn('status', ['pending', 'waiting_designer', 'in_progress', 'deliverable_sent', 'revision_needed'])
            ->count();
        $totalSelesai    = Order::where('designer_id', Auth::id())
            ->where('status', 'completed')
            ->count();

        return view('designer.pekerjaan.daftar-pekerjaan', [
            'pekerjaan'       => $pekerjaan,
            'kataKunci'       => $kataKunci,
            'status'          => $status,
            'totalSemua'      => $totalSemua,
            'totalDikerjakan' => $totalDikerjakan,
            'totalSelesai'    => $totalSelesai,
        ]);
    }

    /**
     * Detail pekerjaan + chat dengan customer.
     */
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
        ])
            ->where('designer_id', Auth::id())
            ->findOrFail($order);

        return view('designer.pekerjaan.detail-pekerjaan', compact('order'));
    }

    /**
     * Kirim pesan dari designer.
     */
    public function kirimPesan(Request $request, string $order)
    {
        $request->validate([
            'isi'        => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|max:25600|mimes:png,jpg,jpeg,gif,webp,pdf,zip,doc,docx',
        ]);

        if (!$request->filled('isi') && !$request->hasFile('attachment')) {
            return back()->withErrors(['isi' => 'Pesan atau lampiran harus diisi.']);
        }

        $orderModel = Order::where('designer_id', Auth::id())->findOrFail($order);

        $conversation = $orderModel->conversation;

        if (!$conversation) {
            $conversation = Conversation::create([
                'order_id'    => $orderModel->id,
                'customer_id' => $orderModel->customer_id,
                'designer_id' => $orderModel->designer_id,
                'status'      => 'active',
            ]);
        }

        $messageType = $request->hasFile('attachment') ? 'file' : 'text';

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'type'            => $messageType,
            'body'            => $request->isi ?? '',
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

        $conversation->touch();

        return redirect()
            ->route('designer.pekerjaan.show', $orderModel->id)
            ->with('status', 'Pesan terkirim.');
    }

    public function downloadAttachment(string $order, string $attachment)
    {
        $orderModel = Order::where('designer_id', Auth::id())->findOrFail($order);

        $att = MessageAttachment::whereHas('message.conversation', function ($q) use ($orderModel) {
                $q->where('order_id', $orderModel->id);
            })
            ->where('id', $attachment)
            ->firstOrFail();

        $response = Http::withOptions(['stream' => true, 'verify' => false])
            ->get(\App\Helpers\StorageHelper::url($att->file_url));

        if ($response->failed()) {
            abort(404, 'Gagal mengambil file.');
        }

        return response()->streamDownload(function () use ($response) {
            echo $response->body();
        }, $att->file_name ?? 'file');
    }

    public function downloadReference(string $order, string $reference)
    {
        $orderModel = Order::where('designer_id', Auth::id())->findOrFail($order);

        $ref = OrderReference::where('order_id', $orderModel->id)
            ->where('id', $reference)
            ->firstOrFail();

        if ($ref->type !== 'file' || !$ref->file_url) {
            abort(404, 'File tidak ditemukan.');
        }

        $response = Http::withOptions(['stream' => true, 'verify' => false])
            ->get(\App\Helpers\StorageHelper::url($ref->file_url));

        if ($response->failed()) {
            abort(404, 'Gagal mengambil file.');
        }

        return response()->streamDownload(function () use ($response) {
            echo $response->body();
        }, $ref->file_name ?? 'file');
    }

}