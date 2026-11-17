<?php

namespace App\Models;

use App\Enums\BillingPeriod;
use App\Models\Concerns\HasUuidsV7;
use Database\Factories\MembershipPlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipPlan extends Model
{
    /** @use HasFactory<MembershipPlanFactory> */
    use HasFactory, HasUuidsV7;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'price',
        'billing_period',
        'duration_days',
        'description',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'billing_period' => BillingPeriod::class,
            'duration_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }
}