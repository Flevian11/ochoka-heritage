<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class MembershipApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_application_id', 'organization_id', 'uploaded_by',
        'document_type', 'original_name', 'storage_disk', 'storage_path',
        'mime_type', 'size_bytes', 'metadata',
    ];

    protected function casts(): array { return ['metadata' => 'array']; }

    public function application(): BelongsTo { return $this->belongsTo(MembershipApplication::class, 'membership_application_id'); }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }

    protected static function booted(): void
    {
        static::saving(function (MembershipApplicationDocument $document): void {
            $application = MembershipApplication::find($document->membership_application_id);
            if ($application && (int) $application->organization_id !== (int) $document->organization_id) {
                throw new \InvalidArgumentException('The document and application must belong to the same organization.');
            }
            if ($document->uploaded_by) {
                $uploader = User::find($document->uploaded_by);
                if ($uploader?->member && (int) $uploader->member->organization_id !== (int) $document->organization_id) {
                    throw new \InvalidArgumentException('The uploader must belong to the same organization.');
                }
            }
        });
    }
}
