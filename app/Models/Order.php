<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'invoice_no', 'customer_name', 'customer_whatsapp',
        'service_id', 'service_name', 'talent_id', 'talent_name',
        'quantity', 'price', 'discount', 'commission', 'disbursed', 'total',
        'payment_method', 'status', 'order_date', 'commission_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'commission_date' => 'date',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'commission' => 'decimal:2',
        'disbursed' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUSES = ['pending', 'paid', 'cancelled', 'refunded'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Nomor tampil: invoice (baru) atau kode lama sebagai fallback. */
    public function displayNo(): string
    {
        return $this->invoice_no ?: $this->order_code;
    }

    /** UNTUNG = total − komisi (tidak disimpan, selalu dihitung). */
    public function getProfitAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->commission);
    }

    public function talentDisplay(): string
    {
        return $this->talent?->name ?? $this->talent_name ?? '-';
    }

    public function serviceDisplay(): string
    {
        return $this->service?->name ?? $this->service_name ?? '-';
    }

    protected static function booted(): void
    {
        // Nomor invoice berurutan per bulan, generate saat submit: INV/2026/09/0001
        static::creating(function (self $order) {
            if (empty($order->invoice_no)) {
                $date = $order->order_date ? \Carbon\Carbon::parse($order->order_date) : now();
                $prefix = 'INV/' . $date->format('Y/m') . '/';
                $seq = (int) static::where('invoice_no', 'like', $prefix . '%')->count() + 1;
                while (static::where('invoice_no', $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT))->exists()) {
                    $seq++;
                }
                $order->invoice_no = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function (self $order) {
            // Kalau order sudah punya items, total & komisi = agregat items
            if ($order->exists && $order->items()->exists()) {
                $order->total = (float) $order->items()->selectRaw('COALESCE(SUM(quantity * price - discount), 0) as t')->value('t');
                $order->commission = (float) $order->items()->sum('commission');
            } elseif ($order->total === null || $order->total === '') {
                // Legacy / seeder: hitung dari kolom satuan
                $order->total = ($order->quantity * $order->price) - $order->discount;
            }
            if ($order->total < 0) {
                $order->total = 0;
            }
            // DANA CAIR otomatis = UNTUNG = total − komisi (tidak diinput manual)
            $order->disbursed = max(0, (float) $order->total - (float) $order->commission);
            if (empty($order->order_code)) {
                $order->order_code = 'PV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            }
        });
    }
}
