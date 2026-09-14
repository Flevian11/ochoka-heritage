<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingDecision extends Model
{
    use HasFactory;

    protected $fillable = ['meeting_id', 'sequence', 'description', 'resolution'];

    public function meeting(): BelongsTo { return $this->belongsTo(Meeting::class); }
}
