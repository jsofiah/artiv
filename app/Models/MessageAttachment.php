<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageAttachment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'message_id',
        'file_url',
        'file_name',
        'file_size',
        'mime_type',
        'file_type',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}