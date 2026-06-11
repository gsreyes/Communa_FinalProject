<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Unit;
use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Bill::with(['user', 'unit', 'billType'])->latest()->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'bill_type_id' => 'required|exists:bill_types,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'required|date',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date|after_or_equal:billing_period_start',
            'proof_of_payment' => 'nullable|string',
            'status' => 'nullable|in:Unpaid,Paid',
        ]);

        $resident = Unit::findOrFail($validatedData['unit_id'])->activeResidents()->first();

        if (! $resident) {
            return response()->json(['message' => 'No active resident is assigned to this unit.'], 422);
        }

        $validatedData['user_id'] = $resident->id;
        $validatedData['status'] = $validatedData['status'] ?? 'Unpaid';

        $bill = Bill::create($validatedData);

        return response()->json([
            'message' => 'Bill created successfully',
            'data' => $bill->load(['user', 'unit', 'billType']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bill $bill)
    {
        return response()->json($bill->load(['user', 'unit', 'billType', 'payments']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bill $bill)
    {
        $validatedData = $request->validate([
            'bill_type_id' => 'sometimes|required|exists:bill_types,id',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'sometimes|required|date',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date|after_or_equal:billing_period_start',
            'proof_of_payment' => 'nullable|string',
            'status' => 'sometimes|required|in:Unpaid,Paid',
        ]);

        $bill->update($validatedData);

        return response()->json([
            'message' => 'Bill updated successfully',
            'data' => $bill->fresh()->load(['user', 'unit', 'billType']),
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill)
    {
        $bill->delete();

        return response()->json([
            'message' => 'Bill deleted successfully',
        ]);
    }
}
