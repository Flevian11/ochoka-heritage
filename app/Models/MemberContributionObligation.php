<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberContributionObligation extends Model
{
    use HasFactory;

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'organization_id', 'member_id', 'contribution_rule_id', 'period',
        'amount_due', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'period' => 'date',
            'amount_due' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function contributionRule(): BelongsTo { return $this->belongsTo(ContributionRule::class); }
    public function allocations(): HasMany { return $this->hasMany(ContributionPaymentAllocation::class); }

    public function amountPaid(): float
    {
        return (float) $this->allocations()->whereHas('payment', fn ($query) => $query->where('status', ContributionPayment::STATUS_VERIFIED))->sum('amount');
    }

    public function balance(): float
    {
        return max(0, (float) $this->amount_due - $this->amountPaid());
    }

    public function refreshStatus(): static
    {
        $paid = $this->amountPaid();
        $this->status = $paid >= (float) $this->amount_due
            ? self::STATUS_PAID
            : ($paid > 0 ? self::STATUS_PARTIAL : self::STATUS_UNPAID);
        $this->save();

        return $this;
    }
}
