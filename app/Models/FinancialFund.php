<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialFund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'name', 'slug', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function transactions() { return $this->hasMany(FinancialTransaction::class); }
}
