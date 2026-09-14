<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionVoterEligibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id','member_id','eligible','criteria_snapshot','snapshotted_at',
    ];

    protected $casts = [
        'eligible' => 'boolean',
        'criteria_snapshot' => 'array',
        'snapshotted_at' => 'datetime',
    ];

    public function election() { return $this->belongsTo(Election::class); }
    public function member() { return $this->belongsTo(Member::class); }
}