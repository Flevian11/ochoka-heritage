<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingActionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id', 'responsible_member_id', 'description',
        'status', 'due_date', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function meeting(): BelongsTo { return $this->belongsTo(Meeting::class); }
    public function responsibleMember(): BelongsTo { return $this->belongsTo(Member::class, 'responsible_member_id'); }
}
