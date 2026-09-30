@extends('layouts.designer')

@section('title', 'Detail Pekerjaan')

@php
    $statusMap = [
        'pending'          => ['bg-amber-50 text-amber-600',     'Menunggu Konfirmasi'],
        'waiting_designer' => ['bg-amber-50 text-amber-600',     'Mencari Desainer'],
        'in_progress'      => ['bg-blue-50 text-blue-600',       'Sedang Dikerjakan'],
        'deliverable_sent' => ['bg-violet-50 text-violet-600',   'Menunggu Review'],
        'revision_needed'  => ['bg-orange-50 text-orange-600',   'Perlu Revisi'],
        'completed'        => ['bg-emerald-50 text-emerald-600', 'Selesai'],
        'cancelled'        => ['bg-red-50 text-red-600',         'Dibatalkan'],
    ];
    $logMap = [
        'pending'          => ['Menunggu Konfirmasi',  'bg-amber-100 text-amber-700'],
        'in_progress'      => ['Sedang Dikerjakan',     'bg-blue-100 text-blue-700'],
        'deliverable_sent' => ['Draf Dikirim',          'bg-violet-100 text-violet-700'],
        'revision_needed'  => ['Revisi Diminta',        'bg-orange-100 text-orange-700'],
        'completed'        => ['Pesanan Selesai',       'bg-emerald-100 text-emerald-700'],
    ];
    $formatLogTime = fn($d) => $d ? $d->translatedFormat('d M Y, H:i') . ' WIB' : '-';
@endphp

@section('content')
<div class="w-full py-8">

    {{-- Tombol Kembali --}}
    <a href="{{ route('designer.pekerjaan.index') }}"
       class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-[#6D28D9] mb-4 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12l7.5-7.5M3 12h18"/>
        </svg>
        Kembali ke Pekerjaan Saya
    </a>

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Detail Pekerjaan</h1>
        <p class="text-slate-500 mt-1">
            {{ $order->order_code }} · Kelola pengerjaan dan komunikasi dengan customer.
        </p>
    </div>

    @php
        $customer = $order->customer;
        $designer = $order->designer;
        $conv     = $order->conversation;
    @endphp

    {{-- ============ KARTU-KARTU INFO (3 KOTAK) ============ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        {{-- 1. Layanan + Status --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <h2 class="text-xs font-bold tracking-wide text-slate-400 mb-3">LAYANAN</h2>
            <div class="flex gap-3">
                <div class="w-12 h-12 rounded-xl bg-[#6D28D9] text-white font-bold flex items-center justify-center shrink-0 overflow-hidden">
                    @php $thumbUrl = \App\Helpers\StorageHelper::url($order->product->thumbnail_url); @endphp
                    @if ($thumbUrl)
                        <img src="{{ $thumbUrl }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($order->product->name, 0, 1)) }}
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900 leading-snug line-clamp-2">{{ $order->product->name }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $order->productTier->name }}@if ($order->is_express) · Express @endif
                    </p>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500">Status</span>
                @php [$cls, $label] = $statusMap[$order->status] ?? ['bg-slate-50 text-slate-500', $order->status]; @endphp
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ $cls }} text-[10px] font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ $label }}
                </span>
            </div>
            <div class="mt-2 text-sm font-bold text-[#6D28D9]">
                Rp {{ number_format($order->total_price, 0, ',', '.') }}
            </div>
        </div>

        {{-- 2. Customer + Tenggat --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xs font-bold tracking-wide text-slate-400">CUSTOMER</h2>
                @if ($order->is_express)
                    <span class="px-2 py-0.5 rounded-md bg-lime-100 text-lime-700 text-[10px] font-bold">EXPRESS</span>
                @endif
            </div>

            @if ($customer)
                @php $custAvatar = \App\Helpers\StorageHelper::url($customer->avatar_url); @endphp
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-violet-100 text-[#6D28D9] font-bold flex items-center justify-center overflow-hidden shrink-0">
                        @if ($custAvatar)
                            <img src="{{ $custAvatar }}" alt="" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($customer->full_name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-900 truncate">{{ $customer->full_name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $customer->email }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-2 mt-4">
                <div class="bg-slate-50 rounded-lg p-2.5">
                    <p class="text-[10px] text-slate-500 mb-0.5">Tenggat</p>
                    <p class="font-bold text-slate-900 text-xs">
                        {{ $order->deadline?->translatedFormat('d M') ?? '-' }}
                    </p>
                </div>
                <div class="bg-slate-50 rounded-lg p-2.5">
                    <p class="text-[10px] text-slate-500 mb-0.5">Sisa Waktu</p>
                    @if ($order->deadline && $order->status !== 'completed')
                        <p class="font-bold text-[#6D28D9] text-xs">
                            {{ $order->deadline->diffForHumans(now(), true) }}
                        </p>
                    @else
                        <p class="font-bold text-slate-400 text-xs">-</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 3. Brief + Referensi --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xs font-bold tracking-wide text-slate-400">BRIEF & REFERENSI</h2>
                @if ($order->brief_note)
                    <button type="button"
                            onclick="document.getElementById('brief-modal').classList.remove('hidden')"
                            class="text-xs font-semibold text-[#6D28D9] hover:underline">
                        Detail
                    </button>
                @endif
            </div>

            {{-- Brief --}}
            <div class="mb-3">
                @if ($order->brief_note)
                    <p class="text-sm text-slate-600 leading-relaxed line-clamp-3">
                        {{ $order->brief_note }}
                    </p>
                @else
                    <p class="text-sm text-slate-400 italic">Tidak ada brief.</p>
                @endif
            </div>

            {{-- Referensi --}}
            @if ($order->references->isNotEmpty())
                <div class="mt-auto pt-3 border-t border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 mb-2 uppercase tracking-wide">
                        {{ $order->references->count() }} Berkas Referensi
                    </p>
                    <div class="space-y-1">
                        @foreach ($order->references->take(2) as $ref)
                            <div class="flex items-center justify-between bg-slate-50 rounded-lg p-2">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-slate-800 truncate">
                                        {{ $ref->file_name ?? $ref->external_url }}
                                    </p>
                                </div>
                                @if ($ref->type === 'file' && $ref->file_url)
                                    <a href="{{ route('designer.pekerjaan.reference.download', ['order' => $order->id, 'reference' => $ref->id]) }}"
                                    class="text-slate-400 hover:text-[#6D28D9] shrink-0 ml-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                        @if ($order->references->count() > 2)
                            <p class="text-[10px] text-slate-400 text-center pt-1">
                                +{{ $order->references->count() - 2 }} berkas lagi
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- ============ CHAT (FULL WIDTH) ============ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col h-[calc(100vh-200px)] min-h-[500px]">

        {{-- Header chat --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            @php $custAvatar = \App\Helpers\StorageHelper::url($customer?->avatar_url); @endphp
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-violet-100 text-[#6D28D9] font-bold flex items-center justify-center overflow-hidden">
                    @if ($custAvatar)
                        <img src="{{ $custAvatar }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ $customer ? strtoupper(substr($customer->full_name, 0, 1)) : '?' }}
                    @endif
                </div>
                <div>
                    <p class="font-bold text-slate-900 leading-tight">
                        {{ $customer->full_name ?? 'Customer' }}
                    </p>
                    <p class="text-xs text-slate-500">Customer</p>
                </div>
            </div>
        </div>

        {{-- Isi chat --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4" id="chat-container">
            @forelse ($conv?->messages ?? [] as $msg)
                @php $isMe = $msg->sender_id === auth()->id(); @endphp

                @if ($isMe)
                    {{-- Designer (kanan, ungu) --}}
                    <div class="flex flex-col items-end">
                        <p class="text-xs font-semibold text-slate-500 mb-1.5">
                            {{ $msg->created_at->format('H:i') }} WIB
                            <span class="font-normal text-slate-400">Anda</span>
                        </p>
                        <div class="max-w-[60%] bg-[#6D28D9] text-white rounded-2xl rounded-tr-sm px-4 py-3">
                            <p class="text-sm leading-relaxed whitespace-pre-line">{{ $msg->body }}</p>
                            @foreach ($msg->attachments as $att)
                                <div class="mt-3 bg-white/10 border border-white/20 rounded-xl p-3 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate">{{ $att->file_name }}</p>
                                        <p class="text-xs text-violet-100">{{ number_format(($att->file_size ?? 0) / 1024 / 1024, 1) }} MB</p>
                                    </div>
                                    <a href="{{ route('designer.pekerjaan.attachment.download', ['order' => $order->id, 'attachment' => $att->id]) }}"
                                    class="shrink-0 text-xs font-bold px-3 py-1.5 rounded-lg bg-lime-300 text-lime-900 hover:bg-lime-400">
                                        Unduh
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Customer (kiri, abu) --}}
                    <div class="max-w-[60%]">
                        <p class="text-xs font-semibold text-slate-500 mb-1.5">
                            {{ $msg->sender->full_name ?? 'Customer' }}
                            <span class="font-normal text-slate-400">{{ $msg->created_at->format('H:i') }} WIB</span>
                        </p>
                        <div class="bg-slate-50 rounded-2xl rounded-tl-sm px-4 py-3">
                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $msg->body }}</p>
                            @foreach ($msg->attachments as $att)
                                <div class="mt-3 bg-white border border-slate-200 rounded-xl p-3 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate">{{ $att->file_name }}</p>
                                        <p class="text-xs text-slate-400">{{ number_format(($att->file_size ?? 0) / 1024 / 1024, 1) }} MB</p>
                                    </div>
                                    <a href="{{ route('designer.pekerjaan.attachment.download', ['order' => $order->id, 'attachment' => $att->id]) }}"
                                    class="shrink-0 text-xs font-bold px-3 py-1.5 rounded-lg bg-lime-300 text-lime-900 hover:bg-lime-400">
                                        Unduh
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                <div class="flex items-center justify-center h-full">
                    <p class="text-sm text-slate-400">Belum ada pesan.</p>
                </div>
            @endforelse
        </div>

        {{-- Form kirim (sama seperti sebelumnya) --}}
        <form method="POST"
              action="{{ route('designer.pekerjaan.kirimPesan', $order->id) }}"
              enctype="multipart/form-data"
              class="border-t border-slate-100 px-4 py-3 relative">
            @csrf

            <div id="file-preview" class="hidden mb-2 px-2">
                <div class="inline-flex items-center gap-2 bg-slate-100 rounded-full pl-3 pr-2 py-1.5 text-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5l4-4a3 3 0 10-4.24-4.24l-6 6a3 3 0 000 4.24m3 3.5l-4 4a3 3 0 11-4.24-4.24l6-6a3 3 0 014.24 0"/>
                    </svg>
                    <span id="file-name" class="text-slate-700 max-w-[200px] truncate"></span>
                    <button type="button" onclick="clearFileInput()"
                            class="w-6 h-6 rounded-full hover:bg-slate-200 flex items-center justify-center text-slate-500">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Lampirkan --}}
                <button type="button"
                        onclick="document.getElementById('attachment-input').click()"
                        class="shrink-0 w-10 h-10 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500 hover:text-[#6D28D9] transition"
                        title="Lampirkan file">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                    </svg>
                </button>

                <input type="file" id="attachment-input" name="attachment" class="hidden"
                       accept="*/*" onchange="showFilePreview(this)">

                {{-- Input teks --}}
                <input type="text" name="isi" id="chat-input"
                       placeholder="Tulis pesan untuk customer..."
                       class="flex-1 bg-slate-50 rounded-full px-4 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6D28D9]/30">

                {{-- Emoji --}}
                <button type="button"
                        onclick="document.getElementById('emoji-picker').classList.toggle('hidden')"
                        class="shrink-0 w-10 h-10 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500 hover:text-[#6D28D9] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>
                </button>

                {{-- Kirim --}}
                <button type="submit"
                        class="shrink-0 inline-flex items-center gap-1.5 bg-[#6D28D9] hover:bg-[#5B21B6] text-white text-sm font-semibold px-4 py-2.5 rounded-full transition">
                    <span class="hidden sm:inline">Kirim</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                    </svg>
                </button>

                {{-- Emoji picker --}}
                <div id="emoji-picker"
                     class="hidden absolute bottom-20 right-6 bg-white rounded-2xl shadow-xl border border-slate-200 p-3 z-40 w-[280px]">
                    <div class="grid grid-cols-8 gap-1 max-h-48 overflow-y-auto">
                        @foreach (['😀','😃','😄','😁','😅','😂','🤣','😊','😇','🙂','🙃','😉','😌','😍','🥰','😘','😗','😙','😚','😋','😛','😝','😜','🤪','🤨','🧐','🤓','😎','🤩','🥳','😏','😒','😞','😔','😟','😕','🙁','😣','😖','😫','😩','🥺','😢','😭','😤','😠','😡','🤬','🤯','😳','🥵','🥶','😱','😨','😰','😥','😓','🤗','🤔','🤭','🤫','🤥','😶','😐','😑','😬','🙄','😯','😦','😧','😮','😲','🥱','😴','🤤','😪','😵','🤐','🥴','🤢','🤮','🤧','😷','🤒','🤕','🤑','🤠','😈','👿','👹','👺','🤡','💩','👻','💀','👽','👾','🤖','🎃'] as $emoji)
                            <button type="button"
                                    onclick="insertEmoji('{{ $emoji }}')"
                                    class="w-8 h-8 rounded-lg hover:bg-slate-100 text-lg flex items-center justify-center transition">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>

            </div>

            <p class="text-xs text-slate-400 mt-2 ml-1">
                Format didukung: PNG, JPG, ZIP, PDF (Maks. 25 MB)
            </p>
        </form>
    </div>

</div>

{{-- Modal Brief --}}
@if ($order->brief_note)
    <div id="brief-modal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[85vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900">Brief Pekerjaan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $order->product->name }} · {{ $order->order_code }}</p>
                </div>
                <button type="button"
                        onclick="document.getElementById('brief-modal').classList.add('hidden')"
                        class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-5">
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $order->brief_note }}</p>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                <button type="button"
                        onclick="document.getElementById('brief-modal').classList.add('hidden')"
                        class="px-5 py-2 rounded-xl bg-[#6D28D9] text-white text-sm font-semibold hover:bg-[#5B21B6]">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const conversationId = '{{ $order->conversation?->id }}';
    if (!conversationId || !window.Echo) return;

    window.Echo.private(`chat.${conversationId}`)
        .listen('.message.sent', (e) => {
            if (e.sender.id === '{{ auth()->id() }}') return;
            appendMessage(e);
        });
});

function appendMessage(e) {
    const container = document.getElementById('chat-container');
    if (!container) return;

    const bubble = document.createElement('div');
    bubble.className = 'max-w-[60%]';
    bubble.innerHTML = `
        <p class="text-xs font-semibold text-slate-500 mb-1.5">
            ${e.sender.full_name}
            <span class="font-normal text-slate-400">
                ${new Date(e.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})} WIB
            </span>
        </p>
        <div class="bg-slate-50 rounded-2xl rounded-tl-sm px-4 py-3">
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">${e.body}</p>
        </div>
    `;
    container.appendChild(bubble);
    container.scrollTop = container.scrollHeight;
}

function showFilePreview(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('file-name').textContent =
            file.name + ' (' + formatBytes(file.size) + ')';
        document.getElementById('file-preview').classList.remove('hidden');
    }
}

function clearFileInput() {
    document.getElementById('attachment-input').value = '';
    document.getElementById('file-preview').classList.add('hidden');
}

function insertEmoji(emoji) {
    const input = document.getElementById('chat-input');
    const start = input.selectionStart;
    const end = input.selectionEnd;
    input.value = input.value.substring(0, start) + emoji + input.value.substring(end);
    input.selectionStart = input.selectionEnd = start + emoji.length;
    input.focus();
}

function formatBytes(bytes) {
    if (!bytes) return '0 B';
    const units = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + units[i];
}

document.addEventListener('click', (e) => {
    const picker = document.getElementById('emoji-picker');
    const btn = e.target.closest('button[title="Emoji"]');
    if (picker && !picker.contains(e.target) && !btn) {
        picker.classList.add('hidden');
    }
});
</script>
@endpush