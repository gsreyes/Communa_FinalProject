<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Bill::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'proof_of_payment' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $bill = Bill::create($validatedData);
        return response()->json([
            'message' => 'Bill created successfully',
            'data' => $bill
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bill = Bill::findOrFail($id);
        return response()->json($bill, 200);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bill = Bill::findOrFail($id);

        $validatedData = $request->validate([
            'amount' => 'sometimes|required|numeric',
            'due_date' => 'sometimes|required|date',
            'proof_of_payment' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,paid,unpaid',
        ]);
        $bill->update($validatedData);
        return response()->json([
            'message' => 'Bill updated successfully',
            'data' => $bill
        ], 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bill = Bill::findOrFail($id);
        $bill->delete();
        return response()->json([
            'message' => 'Bill deleted successfully'
        ], 200);
    }
}
