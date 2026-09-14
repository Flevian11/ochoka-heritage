<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialExpenditure extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_REVERSED = 'reversed';

    protected $fillable = [
        'organization_id', 'financial_fund_id', 'ledger_transaction_id',
        'category', 'description', 'amount', 'spent_on', 'payee',
        'payment_method', 'reference', 'status', 'recorded_by',
        'approved_by', 'approved_at', 'paid_by', 'paid_at',
        'reversed_by', 'reversed_at', 'reversal_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent_on' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function fund() { return $this->belongsTo(FinancialFund::class, 'financial_fund_id'); }
    public function ledgerTransaction() { return $this->belongsTo(FinancialTransaction::class, 'ledger_transaction_id'); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function payer() { return $this->belongsTo(User::class, 'paid_by'); }
    public function reverser() { return $this->belongsTo(User::class, 'reversed_by'); }
}
