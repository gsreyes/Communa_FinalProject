<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Unit extends Model
{
    protected $fillable = [
        'unit_number',
        'area_sqm',
    ];

    /**
     * Get the residents (users) associated with this unit
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'unit_users')
            ->withPivot('occupant_type', 'move_in_date', 'move_out_date', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get the tickets for this unit
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the bills for this unit
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Get active residents for this unit
     */
    public function activeResidents(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('is_active', true);
    }
}
