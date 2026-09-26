<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'action', 'module',
        'description', 'url', 'method', 'ip_address', 'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, string $module, ?string $description = null): void
    {
        try {
            $req = request();
            static::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? $req->input('login') ?? $req->input('email'),
                'action' => $action,
                'module' => $module,
                'description' => $description ? mb_substr($description, 0, 500) : null,
                'url' => mb_substr($req->path() . ($req->getQueryString() ? '?' . $req->getQueryString() : ''), 0, 500),
                'method' => $req->method(),
                'ip_address' => $req->ip(),
                'user_agent' => mb_substr((string) $req->userAgent(), 0, 500),
            ]);
        } catch (\Throwable $e) {
            // jangan bikin request gagal gara-gara log
        }
    }
}
