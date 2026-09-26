<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'default_price', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
