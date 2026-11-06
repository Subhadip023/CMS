<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageRevision extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'page_id',
        'user_id',
        'revision_number',
        'title',
        'content',
        'global_styles',
        'is_published_snapshot',
        'summary',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'global_styles' => 'array',
            'is_published_snapshot' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
