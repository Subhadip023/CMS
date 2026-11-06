<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WebsitePlugin extends Pivot
{
    protected $table = 'website_plugins';

    protected $casts = [
        'is_enabled' => 'boolean',
        'config' => 'array',
    ];
}
