<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_category_id',
        'unit_id',
        'type',
        'description',
        'attachment',
        'status',
        'admin_response',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the resident who submitted this ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the unit associated with this ticket
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the category of this ticket
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    /**
     * Get the admin assigned to this ticket
     */
    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope to get only open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope to get resolved tickets
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'Resolved');
    }

    /**
     * Scope to get tickets by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
