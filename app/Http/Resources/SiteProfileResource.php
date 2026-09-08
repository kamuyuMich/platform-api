<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'credentials' => $this->credentials,
            'title' => $this->title,
            'bio' => $this->bio,
            'short_bio' => $this->short_bio,
            'years_experience' => $this->years_experience,
            'specialties' => $this->specialties ?? [],
            'photo_url' => $this->photo_url,
            'email' => $this->email,
            'phone' => $this->phone,
            'linkedin_url' => $this->linkedin_url,
            'twitter_url' => $this->twitter_url,
            'youtube_url' => $this->youtube_url,
            'booking_url' => $this->booking_url,
        ];
    }
}
