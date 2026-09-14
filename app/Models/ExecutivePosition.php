<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExecutivePosition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable=['organization_id','name','slug','description','display_order','is_active'];

    protected function casts(): array { return ['display_order'=>'integer','is_active'=>'boolean']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function appointments(): HasMany { return $this->hasMany(ExecutiveAppointment::class); }

    protected static function booted(): void {
        static::creating(fn(self $position)=>$position->ensureOrganizationIntegrity());
        static::updating(fn(self $position)=>$position->ensureOrganizationIntegrity());
    }

    private function ensureOrganizationIntegrity(): void {
        $organization=Organization::query()->find($this->organization_id);
        if(!$organization) return;
        if(!$organization->is_active) throw new \InvalidArgumentException('Executive position must belong to an active organization.');
    }
}
