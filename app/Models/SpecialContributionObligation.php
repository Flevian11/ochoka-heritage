<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialContributionObligation extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';
    public const STATUS_WAIVED = 'waived';

    protected $fillable = [
        'special_contribution_id', 'member_id', 'amount', 'paid_amount',
        'status', 'waived_at', 'waived_by', 'waiver_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'waived_at' => 'datetime',
    ];

    public function specialContribution() { return $this->belongsTo(SpecialContribution::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function waivedBy() { return $this->belongsTo(User::class, 'waived_by'); }

    public function outstandingAmount(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }
}
