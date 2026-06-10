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
        return response()->json(Unit::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'unit_number' => 'required|string',
            'area_sqm' => 'required|numeric',
        ]);

        $unit = Unit::create($validatedData);
        return response()->json([
            'message' => 'Unit created successfully',
            'data' => $unit
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json($unit, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $unit = Unit::findOrFail($id);

        $validatedData = $request->validate([
            'unit_number' => 'sometimes|required|string',
            'area_sqm' => 'sometimes|required|numeric',
        ]);

        $unit->update($validatedData);
        return response()->json([
            'message' => 'Unit updated successfully',
            'data' => $unit
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return response()->json([
            'message' => 'Unit deleted successfully'
        ], 200);
    }
}
