<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContributionRule extends Model
{
    use HasFactory;

    public const FREQUENCY_MONTHLY = 'monthly';

    protected $fillable = [
        'organization_id', 'name', 'description', 'amount', 'frequency',
        'effective_from', 'effective_to', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function obligations(): HasMany
    {
        return $this->hasMany(MemberContributionObligation::class);
    }

    public function appliesToPeriod($period): bool
    {
        $period = $period instanceof \Carbon\CarbonInterface ? $period->copy()->startOfMonth() : \Carbon\Carbon::parse($period)->startOfMonth();
        $from = $this->effective_from->copy()->startOfMonth();

        return $this->is_active
            && $period->greaterThanOrEqualTo($from)
            && ($this->effective_to === null || $period->lessThanOrEqualTo($this->effective_to->copy()->startOfMonth()));
    }
}
