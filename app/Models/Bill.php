<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'amount',
        'due_date',
        'proof_of_payment',
        'status',
    ];
}
