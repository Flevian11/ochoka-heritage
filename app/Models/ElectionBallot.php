<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionBallot extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id','voter_member_id','ballot_token','status','cast_at','sealed_at',
    ];

    protected $casts = ['cast_at' => 'datetime', 'sealed_at' => 'datetime'];

    public function election() { return $this->belongsTo(Election::class); }
    public function voter() { return $this->belongsTo(Member::class, 'voter_member_id'); }
    public function votes() { return $this->hasMany(ElectionVote::class, 'ballot_id'); }

    protected static function booted(): void
    {
        static::updating(function (self $ballot) {
            if ($ballot->getOriginal('sealed_at') !== null) {
                throw new \LogicException('A sealed ballot is immutable.');
            }
        });

        static::deleting(function (self $ballot) {
            if ($ballot->sealed_at !== null) {
                throw new \LogicException('A sealed ballot cannot be deleted.');
            }
        });
    }
}