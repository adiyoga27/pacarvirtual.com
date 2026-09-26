<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'label'];

    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function set(string $key, $value, string $group = 'general', string $type = 'text', ?string $label = null): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type, 'label' => $label ?? $key]
        );
    }

    /** Ambil semua settings sebagai array key => value (untuk view). */
    public static function allAsArray(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
