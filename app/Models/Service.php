<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'default_price', 'komisi_talent', 'komisi_tipe', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'default_price' => 'decimal:2', 'komisi_talent' => 'decimal:2'];

    public const KOMISI_TIPE = ['nominal', 'persen'];

    /** Label komisi untuk ditampilkan, cth: "Rp 20.000" atau "30%". */
    public function komisiLabel(): string
    {
        if ($this->komisi_tipe === 'persen') {
            return rtrim(rtrim(number_format((float) $this->komisi_talent, 2, ',', '.'), '0'), ',') . '%';
        }
        return function_exists('rupiah') ? rupiah($this->komisi_talent) : 'Rp ' . number_format((float) $this->komisi_talent, 0, ',', '.');
    }

    /** Estimasi komisi dalam rupiah untuk suatu harga jual. */
    public function komisiRupiah(float $harga): float
    {
        if ($this->komisi_tipe === 'persen') {
            return max(0, $harga * ((float) $this->komisi_talent / 100));
        }
        return max(0, (float) $this->komisi_talent);
    }

    /** Estimasi margin (harga - komisi) untuk harga default. */
    public function marginDefault(): float
    {
        return max(0, (float) $this->default_price - $this->komisiRupiah((float) $this->default_price));
    }
}
