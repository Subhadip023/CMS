<?php

namespace App\Models;

use App\Traits\BelongsToWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    use BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'page_id',
        'form_id',
        'form_name',
        'fields_data',
        'ip_address',
        'user_agent',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'fields_data' => 'array',
            'is_read' => 'boolean',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
