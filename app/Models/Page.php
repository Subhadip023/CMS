<?php

namespace App\Models;

use App\Traits\BelongsToWebsite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes, BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'parent_id',
        'title',
        'slug',
        'template',
        'status',
        'scheduled_at',
        'published_at',
        'is_homepage',
        'access_type',
        'access_password',
        'order_index',
        'layout_draft',
        'layout_published',
        'global_styles',
    ];

    protected function casts(): array
    {
        return [
            'is_homepage' => 'boolean',
            'order_index' => 'integer',
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
            'layout_draft' => 'array',
            'layout_published' => 'array',
            'global_styles' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('order_index');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PageRevision::class)->orderByDesc('revision_number');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'entity');
    }
}
