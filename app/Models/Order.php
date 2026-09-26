<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'customer_name', 'customer_whatsapp',
        'service_name', 'talent_name', 'quantity', 'price', 'discount', 'total',
        'payment_method', 'status', 'order_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUSES = ['pending', 'paid', 'cancelled', 'refunded'];
    public const PAYMENT_METHODS = ['QRIS', 'Transfer Bank', 'E-Wallet', 'Cash', 'Lainnya'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::saving(function (self $order) {
            $order->total = ($order->quantity * $order->price) - $order->discount;
            if ($order->total < 0) {
                $order->total = 0;
            }
            if (empty($order->order_code)) {
                $order->order_code = 'PV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            }
        });
    }
}
