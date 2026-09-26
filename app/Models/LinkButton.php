<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkButton extends Model
{
    protected $fillable = [
        'page_id', 'title', 'url', 'icon_path', 'icon_width',
        'open_new_tab', 'is_active', 'sort_order', 'click_count',
    ];

    protected $casts = [
        'open_new_tab' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(ClickLog::class);
    }
}
