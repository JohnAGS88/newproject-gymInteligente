<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use App\Models\Concerns\BelongsToBranch;
use App\Models\Concerns\HasUuidsV7;
use Carbon\CarbonImmutable;
use Database\Factories\MembershipFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    /** @use HasFactory<MembershipFactory> */
    use BelongsToBranch, HasFactory, HasUuidsV7;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'branch_id',
        'membership_plan_id',
        'status',
        'starts_at',
        'ends_at',
        'frozen_until',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MembershipStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'frozen_until' => 'datetime',
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

    /**
     * @return BelongsTo<MembershipPlan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @param  Builder<Membership>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', MembershipStatus::Active->value);
    }

    /**
     * ¿La membresía está vigente y utilizable en este instante?
     */
    public function isCurrentlyActive(?CarbonImmutable $now = null): bool
    {
        $now ??= CarbonImmutable::now();

        if ($this->status !== MembershipStatus::Active) {
            return false;
        }

        if ($this->starts_at !== null && $this->starts_at->greaterThan($now)) {
            return false;
        }

        if ($this->ends_at !== null && $this->ends_at->lessThanOrEqualTo($now)) {
            return false;
        }

        if ($this->frozen_until !== null && $this->frozen_until->greaterThan($now)) {
            return false;
        }

        return true;
    }
}