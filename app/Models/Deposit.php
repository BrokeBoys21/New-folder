<?php
// app/Models/Deposit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'amount',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}