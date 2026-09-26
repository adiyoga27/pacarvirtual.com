<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'account_number', 'account_name', 'description', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    /** Daftar nama metode aktif berurutan — dipakai dropdown form order & filter laporan. */
    public static function activeNames(): array
    {
        try {
            $names = static::where('is_active', true)->orderBy('sort_order')->orderBy('name')->pluck('name')->all();
            return $names !== [] ? $names : ['QRIS', 'Transfer Bank', 'E-Wallet', 'Cash', 'Lainnya'];
        } catch (\Throwable $e) {
            return ['QRIS', 'Transfer Bank', 'E-Wallet', 'Cash', 'Lainnya'];
        }
    }
}
