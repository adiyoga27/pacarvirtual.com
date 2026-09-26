<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'slug', 'name', 'header_title', 'subtitle',
        'show_logo', 'show_back_button', 'logo_path',
        'use_global_background', 'background_type', 'background_image',
        'background_color', 'background_gradient',
        'meta_title', 'meta_description', 'meta_keywords', 'meta_author',
        'og_image', 'footer_text', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'show_logo' => 'boolean',
        'show_back_button' => 'boolean',
        'use_global_background' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function buttons(): HasMany
    {
        return $this->hasMany(LinkButton::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeButtons(): HasMany
    {
        return $this->hasMany(LinkButton::class)->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
