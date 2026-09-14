<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'executive_position_id',
        'seats',
        'max_votes_per_voter',
        'nomination_limit',
        'eligibility_criteria',
    ];

    protected $casts = [
        'eligibility_criteria' => 'array',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function executivePosition()
    {
        return $this->belongsTo(ExecutivePosition::class);
    }

    public function nominations()
    {
        return $this->hasMany(ElectionNomination::class);
    }

    public function votes()
    {
        return $this->hasMany(ElectionVote::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $electionPosition) {
            $electionPosition->ensureSameOrganization();
        });

        static::updating(function (self $electionPosition) {
            $electionPosition->ensureSameOrganization();
        });
    }

    private function ensureSameOrganization(): void
    {
        $election = Election::query()->find($this->election_id);
        $executivePosition = ExecutivePosition::query()->find($this->executive_position_id);

        if (! $election || ! $executivePosition) {
            return;
        }

        if ((int) $election->organization_id !== (int) $executivePosition->organization_id) {
            throw new \InvalidArgumentException(
                'Election position and executive position must belong to the same organization.'
            );
        }
    }
}
