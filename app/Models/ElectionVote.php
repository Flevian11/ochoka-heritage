<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'ballot_id','election_position_id','candidate_member_id','selection_order',
    ];

    public function ballot() { return $this->belongsTo(ElectionBallot::class, 'ballot_id'); }
    public function electionPosition() { return $this->belongsTo(ElectionPosition::class); }
    public function candidate() { return $this->belongsTo(Member::class, 'candidate_member_id'); }
}