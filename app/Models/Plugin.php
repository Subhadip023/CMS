<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plugin extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'version',
        'description',
        'author',
        'settings_schema',
        'hooks_manifest',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'settings_schema' => 'array',
            'hooks_manifest' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public function websites(): BelongsToMany
    {
        return $this->belongsToMany(Website::class, 'website_plugins')
            ->withPivot(['is_enabled', 'config'])
            ->withTimestamps();
    }
}
