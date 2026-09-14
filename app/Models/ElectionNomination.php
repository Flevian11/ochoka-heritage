<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionNomination extends Model
{
    use HasFactory;

    public const PENDING = 'pending';
    public const VERIFIED = 'verified';
    public const REJECTED = 'rejected';
    public const WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'election_position_id','candidate_member_id','nominated_by','status',
        'statement','eligibility_snapshot','verified_by','verified_at','rejection_reason',
    ];

    protected $casts = [
        'eligibility_snapshot' => 'array',
        'verified_at' => 'datetime',
    ];

    public function electionPosition() { return $this->belongsTo(ElectionPosition::class); }
    public function candidate() { return $this->belongsTo(Member::class, 'candidate_member_id'); }
    public function nominator() { return $this->belongsTo(Member::class, 'nominated_by'); }
    public function seconders() { return $this->hasMany(ElectionNominationSeconder::class, 'nomination_id'); }
}