<?php

namespace App\Traits;

use App\Models\Website;
use App\Scopes\WebsiteScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToWebsite
{
    /**
     * Boot the BelongsToWebsite trait for a model.
     */
    protected static function bootBelongsToWebsite(): void
    {
        static::addGlobalScope(new WebsiteScope);

        static::creating(function ($model) {
            // Auto-assign website_id if not explicitly set and current website context exists
            if (empty($model->website_id) && app()->has('current_website_id')) {
                $model->website_id = app('current_website_id');
            }
        });
    }

    /**
     * Get the website that owns the model.
     */
    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
