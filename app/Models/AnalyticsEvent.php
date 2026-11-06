<?php

namespace App\Models;

use App\Traits\BelongsToWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    use BelongsToWebsite;

    public $timestamps = false;

    protected $fillable = [
        'website_id',
        'page_id',
        'visitor_hash',
        'session_id',
        'event_type',
        'path',
        'referrer',
        'device_type',
        'browser',
        'os',
        'country',
        'city',
        'duration',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
