<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'type',
        'description',
        'attachment',
        'status',
        'admin_response',
    ];
}
