<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_code',
        'customer_id',
        'designer_id',
        'product_id',
        'product_tier_id',
        'unit_price',
        'quantity',
        'deadline',
        'is_express',
        'express_fee_id',
        'express_fee',
        'brief_note',
        'total_price',
        'status',
        'approved_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'deadline' => 'datetime',
        'is_express' => 'boolean',
        'express_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // ==== Relasi ====

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productTier(): BelongsTo
    {
        return $this->belongsTo(ProductTier::class);
    }

    public function expressFee(): BelongsTo
    {
        return $this->belongsTo(ExpressFee::class);
    }

    public function references(): HasMany
    {
        return $this->hasMany(OrderReference::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class);
    }

    public function split(): HasOne
    {
        return $this->hasOne(OrderSplit::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

        public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // ==== Accessor untuk tampilan progres ====

    public function getProgressAttribute(): array
    {
        // GANTI/lengkapi key ini kalau ada status lain di database
        $map = [
            'pending'         => [1, 'Brief Diterima'],
            'in_progress'     => [2, 'Eksplorasi Konsep'],
            'revision_needed' => [2, 'Revisi'],
            'finishing'       => [3, 'Finishing'],
            'completed'       => [3, 'Selesai'],
        ];

        [$step, $label] = $map[$this->status]
            ?? [1, ucfirst(str_replace('_', ' ', $this->status))];

        return [
            'step'    => $step,
            'label'   => $label,
            'percent' => round($step / 3 * 100),
        ];
    }
}