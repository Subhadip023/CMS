<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'subdomain',
        'custom_domain',
        'category',
        'description',
        'status',
        'is_published',
        'published_at',
        'logo_url',
        'favicon_url',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function homePage(): HasOne
    {
        return $this->hasOne(Page::class)->where('is_homepage', true);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function themeSettings(): HasOne
    {
        return $this->hasOne(WebsiteThemeSetting::class);
    }

    public function plugins(): BelongsToMany
    {
        return $this->belongsToMany(Plugin::class, 'website_plugins')
            ->withPivot(['is_enabled', 'config'])
            ->withTimestamps();
    }

    public function formSubmissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function reusableComponents(): HasMany
    {
        return $this->hasMany(ReusableComponent::class);
    }

    /**
     * Get computed public preview / live URL.
     */
    public function getLiveUrlAttribute(): string
    {
        if (!empty($this->custom_domain)) {
            return 'https://' . $this->custom_domain;
        }

        return url('/site/' . $this->subdomain);
    }
}
