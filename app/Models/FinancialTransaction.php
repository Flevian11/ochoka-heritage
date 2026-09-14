<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    public const TYPE_CONTRIBUTION = 'contribution';
    public const TYPE_WELFARE = 'welfare';
    public const TYPE_SPECIAL_CONTRIBUTION = 'special_contribution';
    public const TYPE_EXPENDITURE = 'expenditure';
    public const TYPE_ADJUSTMENT = 'adjustment';
    public const TYPE_TRANSFER = 'transfer';

    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';

    public const STATUS_POSTED = 'posted';
    public const STATUS_REVERSED = 'reversed';

    protected $fillable = [
        'organization_id', 'financial_fund_id', 'member_id', 'recorded_by',
        'transaction_number', 'transaction_type', 'direction', 'amount',
        'transaction_date', 'reference', 'description', 'source_type', 'source_id',
        'status', 'posted_at', 'reversed_by', 'reversed_at', 'reversal_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function fund() { return $this->belongsTo(FinancialFund::class, 'financial_fund_id'); }
    public function member() { return $this->belongsTo(Member::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
    public function reversedBy() { return $this->belongsTo(User::class, 'reversed_by'); }

    public function source()
    {
        return $this->morphTo();
    }

    public function isIncome(): bool { return $this->direction === self::DIRECTION_IN; }
    public function isExpense(): bool { return $this->direction === self::DIRECTION_OUT; }
}
