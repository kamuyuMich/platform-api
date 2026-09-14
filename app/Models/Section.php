<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Section extends Model
{
    protected $fillable = ['name', 'slug', 'color', 'order'];

    protected static function booted(): void
    {
        static::creating(function (Section $section) {
            if (empty($section->slug)) {
                $section->slug = Str::slug($section->name);
            }
        });
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}