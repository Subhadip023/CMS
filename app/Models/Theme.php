<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'author',
        'preview_image',
        'default_config',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_config' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function websiteSettings(): HasMany
    {
        return $this->hasMany(WebsiteThemeSetting::class);
    }
}
