<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillType extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
