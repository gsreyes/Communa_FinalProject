<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Ticket::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'attachment' => 'nullable|string',
            'status' => 'required|string|in:open,closed,pending',
            'admin_response' => 'nullable|string',
        ]);

        $ticket = Ticket::create($validatedData);
        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => $ticket
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validatedData = $request->validate([
            'type' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'attachment' => 'nullable|string',
            'status' => 'sometimes|required|string|in:open,closed,pending',
            'admin_response' => 'nullable|string',
        ]);

        $ticket->update($validatedData);
        return response()->json([
            'message' => 'Ticket updated successfully',
            'data' => $ticket
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return response()->json([
            'message' => 'Ticket deleted successfully'
        ], 200);
    }
}
