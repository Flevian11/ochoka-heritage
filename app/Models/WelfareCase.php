<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WelfareCase extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_BEREAVEMENT = 'bereavement';
    public const TYPE_MEDICAL_EMERGENCY = 'medical_emergency';
    public const TYPE_ACCIDENT = 'accident';
    public const TYPE_OTHER_FAMILY_SUPPORT = 'other_family_support';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PROPOSED = 'proposed';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_COLLECTION = 'collection';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'organization_id', 'title', 'slug', 'type', 'description',
        'beneficiary_member_id', 'beneficiary_name', 'target_amount',
        'proposed_support_amount', 'approved_support_amount', 'status',
        'created_by', 'approved_by', 'approved_at', 'starts_on', 'ends_on',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'proposed_support_amount' => 'decimal:2',
        'approved_support_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'starts_on' => 'date',
        'ends_on' => 'date',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function beneficiaryMember() { return $this->belongsTo(Member::class, 'beneficiary_member_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function contributions() { return $this->hasMany(WelfareContribution::class); }
    public function supportObligations() { return $this->hasMany(WelfareSupportObligation::class); }
}
