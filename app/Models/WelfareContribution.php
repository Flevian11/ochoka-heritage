<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelfareContribution extends Model
{
    use HasFactory;

    public const SOURCE_INDEPENDENT_PAYMENT = 'independent_payment';
    public const SOURCE_MEMBER_CONTRIBUTION_BALANCE = 'member_contribution_balance';

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REVERSED = 'reversed';

    protected $fillable = [
        'organization_id', 'welfare_case_id', 'member_id', 'amount',
        'source_type', 'payment_method', 'reference', 'paid_on', 'status',
        'verified_by', 'verified_at', 'reversed_by', 'reversed_at',
        'reversal_reason', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_on' => 'date',
        'verified_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function welfareCase() { return $this->belongsTo(WelfareCase::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }
    public function reverser() { return $this->belongsTo(User::class, 'reversed_by'); }
}
