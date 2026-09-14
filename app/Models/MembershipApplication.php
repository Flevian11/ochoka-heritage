<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipApplication extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'organization_id', 'user_id', 'application_number',
        'first_name', 'middle_name', 'last_name', 'phone', 'alternate_phone',
        'email', 'date_of_birth', 'national_id', 'address', 'city', 'county',
        'eligibility_answers', 'status', 'submitted_at', 'reviewed_by',
        'reviewed_at', 'rejection_reason', 'approved_member_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'eligibility_answers' => 'array',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function approvedMember(): BelongsTo { return $this->belongsTo(Member::class, 'approved_member_id'); }
    public function documents(): HasMany { return $this->hasMany(MembershipApplicationDocument::class); }

    protected static function booted(): void
    {
        static::deleting(fn () => throw new \LogicException('Membership applications are historical records and cannot be deleted.'));
        static::saving(function (MembershipApplication $application): void {
            if ($application->user_id) {
                $user = User::find($application->user_id);
                if ($user?->member && (int) $user->member->organization_id !== (int) $application->organization_id) {
                    throw new \InvalidArgumentException('The application user must belong to the same organization.');
                }
            }
            if ($application->approved_member_id) {
                $member = Member::find($application->approved_member_id);
                if ($member && (int) $member->organization_id !== (int) $application->organization_id) {
                    throw new \InvalidArgumentException('The approved member must belong to the same organization.');
                }
            }
            if ($application->reviewed_by) {
                $reviewer = User::find($application->reviewed_by);
                if ($reviewer?->member && (int) $reviewer->member->organization_id !== (int) $application->organization_id) {
                    throw new \InvalidArgumentException('The reviewer must belong to the same organization.');
                }
            }
        });

    }
}
