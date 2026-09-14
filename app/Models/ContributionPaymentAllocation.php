<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContributionPaymentAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'contribution_payment_id', 'member_contribution_obligation_id', 'amount',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function payment(): BelongsTo { return $this->belongsTo(ContributionPayment::class, 'contribution_payment_id'); }
    public function obligation(): BelongsTo { return $this->belongsTo(MemberContributionObligation::class, 'member_contribution_obligation_id'); }
}
