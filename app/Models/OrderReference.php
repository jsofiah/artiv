<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_id',
        'type',
        'file_url',
        'file_name',
        'file_size',
        'mime_type',
        'external_url',
        'note',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}