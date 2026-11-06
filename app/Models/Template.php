<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Template extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'thumbnail',
        'layout_data',
        'is_system',
        'website_id',
    ];

    protected function casts(): array
    {
        return [
            'layout_data' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
