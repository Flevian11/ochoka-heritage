<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingDocument extends Model
{
    use HasFactory;

    public const TYPE_MINUTES = 'minutes';
    public const TYPE_AGENDA = 'agenda';
    public const TYPE_SUPPORTING = 'supporting';

    protected $fillable = [
        'meeting_id', 'document_type', 'original_name', 'stored_path',
        'mime_type', 'file_size', 'uploaded_by', 'description',
    ];

    public function meeting(): BelongsTo { return $this->belongsTo(Meeting::class); }
    public function uploadedBy(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
