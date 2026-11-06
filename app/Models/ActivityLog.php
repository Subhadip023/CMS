<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'website_id',
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to quickly log an activity.
     */
    public static function record(string $action, ?int $websiteId = null, ?Model $subject = null, ?array $payload = null): self
    {
        return static::create([
            'website_id' => $websiteId ?? (app()->has('current_website_id') ? app('current_website_id') : null),
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => $payload,
        ]);
    }
}
