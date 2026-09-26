<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'service_id', 'service_name', 'talent_id',
        'quantity', 'price', 'discount', 'commission', 'commission_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'commission' => 'decimal:2',
        'commission_date' => 'date',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }

    /** Total baris = qty × harga − diskon. */
    public function getLineTotalAttribute(): float
    {
        return max(0, (int) $this->quantity * (float) $this->price - (float) $this->discount);
    }
}
