<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Unit::with('users')->orderBy('unit_number')->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'unit_number' => 'required|string',
            'area_sqm' => 'required|numeric|min:0.01',
        ]);

        $unit = Unit::create($validatedData);
        return response()->json([
            'message' => 'Unit created successfully',
            'data' => $unit,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return response()->json($unit->load(['users', 'bills', 'tickets']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validatedData = $request->validate([
            'unit_number' => 'sometimes|required|string',
            'area_sqm' => 'sometimes|required|numeric|min:0.01',
        ]);

        $unit->update($validatedData);
        return response()->json([
            'message' => 'Unit updated successfully',
            'data' => $unit->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json([
            'message' => 'Unit deleted successfully',
        ]);
    }
}
