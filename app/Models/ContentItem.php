<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ContentItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Searchable;

    // 'service' = a consulting package/offering (extra: price, duration, what's_included)
    public const TYPES = ['article', 'research', 'project', 'tool', 'resource', 'service'];

    protected $fillable = [
        'type', 'title', 'slug', 'excerpt', 'body',
        'category_id', 'author_id', 'status', 'published_at',
        'meta_title', 'meta_description', 'og_image',
        'extra', 'read_time_minutes',
    ];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ContentItem $item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->title);
            }
        });
    }

    // ── Relationships ─────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // ── Media (featured image, gallery) ──────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(400)->height(250);
        $this->addMediaConversion('card')->width(800)->height(500);
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured_image', 'card') ?: null;
    }

    // ── Scout (search) ───────────────────────────────────

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'body' => strip_tags((string) $this->body),
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->status === 'published';
    }

    // ── Query scopes ──────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeInCategory(Builder $query, string $categorySlug): Builder
    {
        return $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
    }
}
