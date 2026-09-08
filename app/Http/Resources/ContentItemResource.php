<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->when($request->routeIs('*.show'), $this->body),
            'featured_image_url' => $this->featured_image_url,
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
                'section' => $this->category->section,
            ]),
            'author' => $this->whenLoaded('author', fn () => [
                'name' => $this->author->name,
            ]),
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->pluck('name')),
            'published_at' => $this->published_at?->toIso8601String(),
            'read_time_minutes' => $this->read_time_minutes,
            'meta' => [
                'title' => $this->meta_title ?: $this->title,
                'description' => $this->meta_description ?: $this->excerpt,
                'og_image' => $this->og_image ?: $this->featured_image_url,
            ],
            // type-specific fields (research findings, project problem/solution, tool schema, etc.)
            'extra' => $this->extra,
        ];
    }
}
