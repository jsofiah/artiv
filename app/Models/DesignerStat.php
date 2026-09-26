<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignerStat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'designer_id',
        'total_orders',
        'completed_orders',
        'average_rating',
        'total_earning',
    ];

    protected $casts = [
        'total_orders' => 'integer',
        'completed_orders' => 'integer',
        'average_rating' => 'decimal:2',
        'total_earning' => 'decimal:2',
    ];

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id');
    }
}