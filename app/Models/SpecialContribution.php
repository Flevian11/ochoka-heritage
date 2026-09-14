<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialContribution extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'organization_id', 'name', 'slug', 'description', 'target_amount',
        'amount_per_member', 'starts_on', 'ends_on', 'status', 'created_by',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'amount_per_member' => 'decimal:2',
        'starts_on' => 'date',
        'ends_on' => 'date',
        'approved_at' => 'datetime',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function obligations() { return $this->hasMany(SpecialContributionObligation::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
