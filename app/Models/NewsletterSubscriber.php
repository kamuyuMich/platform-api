<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'subscribed_at', 'unsubscribed_at', 'unsubscribe_token'];

    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (NewsletterSubscriber $sub) {
            if (empty($sub->unsubscribe_token)) {
                $sub->unsubscribe_token = Str::random(40);
            }
        });
    }
}
