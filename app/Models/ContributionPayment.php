<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContributionPayment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REVERSED = 'reversed';

    public const METHOD_CASH = 'cash';
    public const METHOD_MPESA = 'mpesa';
    public const METHOD_BANK = 'bank';
    public const METHOD_OTHER = 'other';

    protected $fillable = [
        'organization_id', 'member_id', 'amount', 'received_at', 'method',
        'reference', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'received_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function allocations(): HasMany { return $this->hasMany(ContributionPaymentAllocation::class); }

    public function allocatedAmount(): float
    {
        return (float) $this->allocations()->sum('amount');
    }

    public function unallocatedAmount(): float
    {
        return max(0, (float) $this->amount - $this->allocatedAmount());
    }
}
