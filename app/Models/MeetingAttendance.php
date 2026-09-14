<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id', 'member_id', 'status', 'recorded_at', 'recorded_by', 'notes',
    ];

    protected function casts(): array { return ['recorded_at' => 'datetime']; }

    public function meeting(): BelongsTo { return $this->belongsTo(Meeting::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function recordedBy(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
