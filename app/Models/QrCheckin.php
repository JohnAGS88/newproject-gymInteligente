<?php

namespace App\Models;

use App\Enums\QrCheckinStatus;
use App\Enums\QrCheckinType;
use App\Models\Concerns\BelongsToBranch;
use App\Models\Concerns\HasUuidsV7;
use Database\Factories\QrCheckinFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrCheckin extends Model
{
    /** @use HasFactory<QrCheckinFactory> */
    use BelongsToBranch, HasFactory, HasUuidsV7;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'branch_id',
        'type',
        'device_id',
        'token_jti',
        'checked_in_at',
        'status',
        'ip_address',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => QrCheckinType::class,
            'status' => QrCheckinStatus::class,
            'checked_in_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}