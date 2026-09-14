<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionNominationSeconder extends Model
{
    use HasFactory;

    protected $fillable = ['nomination_id','member_id'];

    public function nomination() { return $this->belongsTo(ElectionNomination::class); }
    public function member() { return $this->belongsTo(Member::class); }
}