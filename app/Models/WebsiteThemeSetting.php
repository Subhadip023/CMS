<?php

namespace App\Models;

use App\Traits\BelongsToWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteThemeSetting extends Model
{
    use BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'theme_id',
        'tokens',
        'custom_css',
        'custom_js',
    ];

    protected function casts(): array
    {
        return [
            'tokens' => 'array',
        ];
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
