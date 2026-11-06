<?php

namespace App\Models;

use App\Traits\BelongsToWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReusableComponent extends Model
{
    use BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'user_id',
        'name',
        'category',
        'thumbnail',
        'tree_data',
    ];

    protected function casts(): array
    {
        return [
            'tree_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
