<?php

namespace App\Models;

use App\Models\Concerns\HasUuidsV7;
use Illuminate\Database\Eloquent\Model;

class StripeWebhookEvent extends Model
{
    use HasUuidsV7;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'event_id',
        'type',
        'payload',
        'processed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }
}