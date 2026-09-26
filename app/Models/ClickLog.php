<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClickLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['link_button_id', 'ip_address', 'user_agent', 'clicked_at'];

    protected $casts = ['clicked_at' => 'datetime'];

    public function button(): BelongsTo
    {
        return $this->belongsTo(LinkButton::class, 'link_button_id');
    }
}
