<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Meeting $meeting): void {
            $meeting->ensureMeetingTypeBelongsToOrganization();
        });

        static::updating(function (Meeting $meeting): void {
            $meeting->ensureMeetingTypeBelongsToOrganization();
        });
    }

    protected function ensureMeetingTypeBelongsToOrganization(): void
    {
        $type = MeetingType::query()->find($this->meeting_type_id);

        if (!$type || (int) $type->organization_id !== (int) $this->organization_id) {
            throw new \InvalidArgumentException('Meeting type must belong to the same organization as the meeting.');
        }
    }

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_MINUTES_PENDING = 'minutes_pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ARCHIVED = 'archived';

    public const FORMAT_PHYSICAL = 'physical';
    public const FORMAT_ONLINE = 'online';
    public const FORMAT_HYBRID = 'hybrid';

    protected $fillable = [
        'organization_id', 'meeting_type_id', 'organizer_id', 'title',
        'description', 'starts_at', 'ends_at', 'location', 'format',
        'google_meet_url', 'agenda', 'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function meetingType(): BelongsTo { return $this->belongsTo(MeetingType::class); }
    public function organizer(): BelongsTo { return $this->belongsTo(User::class, 'organizer_id'); }
    public function participants(): HasMany { return $this->hasMany(MeetingParticipant::class); }
    public function attendances(): HasMany { return $this->hasMany(MeetingAttendance::class); }
    public function decisions(): HasMany { return $this->hasMany(MeetingDecision::class); }
    public function actionItems(): HasMany { return $this->hasMany(MeetingActionItem::class); }
    public function documents(): HasMany { return $this->hasMany(MeetingDocument::class); }
}
