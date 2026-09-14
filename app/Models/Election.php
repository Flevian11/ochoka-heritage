<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Election extends Model
{
    use HasFactory, SoftDeletes;

    public const DRAFT = 'draft';
    public const NOMINATIONS_OPEN = 'nominations_open';
    public const NOMINATIONS_CLOSED = 'nominations_closed';
    public const CANDIDATES_VERIFIED = 'candidates_verified';
    public const VOTING_OPEN = 'voting_open';
    public const VOTING_CLOSED = 'voting_closed';
    public const RESULTS_VERIFIED = 'results_verified';
    public const RESULTS_PUBLISHED = 'results_published';
    public const ARCHIVED = 'archived';

    protected $fillable = [
        'organization_id','name','slug','description','eligibility_criteria',
        'nominations_open_at','nominations_close_at','voting_open_at','voting_close_at',
        'status','created_by','approved_by','approved_at',
    ];

    protected $casts = [
        'eligibility_criteria' => 'array',
        'nominations_open_at' => 'datetime',
        'nominations_close_at' => 'datetime',
        'voting_open_at' => 'datetime',
        'voting_close_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function positions() { return $this->hasMany(ElectionPosition::class); }
    public function voterEligibilities() { return $this->hasMany(ElectionVoterEligibility::class); }
    public function ballots() { return $this->hasMany(ElectionBallot::class); }

    public function isVotingOpen(): bool
    {
        return $this->status === self::VOTING_OPEN;
    }
}