<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'seo_title',
        'seo_description',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Auto-generate slug from title if not provided.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function getEffectiveSeoTitleAttribute(): string
    {
        return $this->seo_title ?: $this->title . ' — Nepal Prize Checker';
    }

    public function getEffectiveSeoDescriptionAttribute(): string
    {
        return $this->seo_description ?: ($this->excerpt ?? '');
    }
}
