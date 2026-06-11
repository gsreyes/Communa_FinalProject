<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillType;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    // Display billing statements for billing staff or resident's bills
    public function index()
    {
        $user = Auth::user();

        if ($user->isBillingStaff() || $user->isAdmin()) {
            // Staff and Admins see all bills
            $bills = Bill::with(['user', 'unit', 'billType'])
                ->latest()
                ->paginate(20);
        } elseif ($user->isResident()) {
            // Resident sees their bills
            $bills = $user->bills()
                ->with(['unit', 'billType'])
                ->latest()
                ->paginate(20);
        } else {
            abort(403);
        }

        $stats = $this->statsFor($user);

        return view('bills.index', compact('bills', 'stats'));
    }

    // Show the form for creating a new bill (billing staff only)
    public function create()
    {
        abort_if(!Auth::user()->isBillingStaff(), 403);

        $billTypes = BillType::where('is_active', true)->get();
        $units = Unit::all();

        return view('bills.create', compact('billTypes', 'units'));
    }

    // Store a newly created bill

    public function store(Request $request)
    {
        abort_if(!Auth::user()->isBillingStaff(), 403);

        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'bill_type_id' => 'required|exists:bill_types,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'required|date|after_or_equal:today',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date|after_or_equal:billing_period_start',
        ]);

        $unit = Unit::find($validated['unit_id']);
        
        // Get the primary resident for the unit (assuming the first active resident is the primary)
        $resident = $unit->activeResidents()->first();

        if (!$resident) {
            return back()->with('error', 'No active residents found for this unit.');
        }

        $validated['user_id'] = $resident->id;
        $validated['status'] = 'Unpaid';

        Bill::create($validated);

        return redirect()->route('bills.index')
            ->with('success', 'Bill created successfully.');
    }

    // Display the specified bill
    public function show(Bill $bill)
    {
        $this->authorize('view', $bill);

        $bill->load(['user', 'unit', 'billType', 'payments']);

        return view('bills.show', compact('bill'));
    }

    // Show the form for editing a bill (billing staff only)
    public function edit(Bill $bill)
    {
        abort_if(!Auth::user()->isBillingStaff(), 403);

        $billTypes = BillType::where('is_active', true)->get();
        $units = Unit::all();

        return view('bills.edit', compact('bill', 'billTypes', 'units'));
    }

    // Update the specified bill
    public function update(Request $request, Bill $bill)
    {
        abort_if(!Auth::user()->isBillingStaff(), 403);

        $validated = $request->validate([
            'bill_type_id' => 'required|exists:bill_types,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'required|date',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date|after_or_equal:billing_period_start',
        ]);

        $bill->update($validated);

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Bill updated successfully.');
    }

    // Delete a bill (billing staff only, only if unpaid)
    public function destroy(Bill $bill)
    {
        abort_if(!Auth::user()->isBillingStaff(), 403);

        if ($bill->status === 'Paid') {
            return back()->with('error', 'Cannot delete a paid bill.');
        }

        $bill->delete();

        return redirect()->route('bills.index')
            ->with('success', 'Bill deleted successfully.');
    }

    // Get billing statistics (for dashboard)
    public function getStats()
    {
        return $this->statsFor(Auth::user());
    }

    private function statsFor($user): array
    {
        $query = ($user->isBillingStaff() || $user->isAdmin()) ? Bill::query() : $user->bills();

        if (!$user->isBillingStaff() && !$user->isResident() && !$user->isAdmin()) {
            return [];
        }

        return [
            'total_bills' => (clone $query)->count(),
            'paid' => (clone $query)->where('status', 'Paid')->sum('amount'),
            'unpaid' => (clone $query)->where('status', 'Unpaid')->sum('amount'),
            'overdue' => (clone $query)->overdue()->sum('amount'),
        ];
    }
}
