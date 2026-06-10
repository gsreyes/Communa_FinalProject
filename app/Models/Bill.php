<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    protected $fillable = [
        'user_id',
        'unit_id',
        'bill_type_id',
        'amount',
        'description',
        'due_date',
        'billing_period_start',
        'billing_period_end',
        'proof_of_payment',
        'status',
        'reference_number',
        'paid_amount',
        'paid_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user (resident) for this bill
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the unit associated with this bill
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the type of this bill
     */
    public function billType(): BelongsTo
    {
        return $this->belongsTo(BillType::class);
    }

    /**
     * Get the payments associated with this bill
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope to get unpaid bills
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'Unpaid');
    }

    /**
     * Scope to get paid bills
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    /**
     * Scope to get overdue bills
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString())
            ->where('status', 'Unpaid');
    }

    /**
     * Check if bill is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now()->toDateString() && $this->status === 'Unpaid';
    }
}
