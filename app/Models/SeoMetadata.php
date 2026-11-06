<?php

namespace App\Models;

use App\Traits\BelongsToWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    use BelongsToWebsite;

    protected $table = 'seo_metadata';

    protected $fillable = [
        'website_id',
        'entity_type',
        'entity_id',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image_url',
        'twitter_card',
        'robots_index',
        'robots_follow',
        'schema_markup',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'schema_markup' => 'array',
        ];
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}
