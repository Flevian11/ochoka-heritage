<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingParticipant extends Model
{
    use HasFactory;

    protected $fillable = ['meeting_id', 'member_id', 'role', 'is_required'];

    protected function casts(): array { return ['is_required' => 'boolean']; }

    public function meeting(): BelongsTo { return $this->belongsTo(Meeting::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}
