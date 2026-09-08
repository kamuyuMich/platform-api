<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteProfile extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'site_profile';

    protected $fillable = [
        'name', 'credentials', 'title', 'bio', 'short_bio', 'years_experience',
        'specialties', 'email', 'phone', 'linkedin_url', 'twitter_url',
        'youtube_url', 'booking_url',
    ];

    protected function casts(): array
    {
        return ['specialties' => 'array'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('square')->width(500)->height(500);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo', 'square') ?: null;
    }

    /**
     * There's only ever one profile row. This fetches it, creating an
     * empty placeholder one on first access so the site never 500s
     * just because nobody's filled the form in yet.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], ['name' => 'Your Name Here']);
    }
}
