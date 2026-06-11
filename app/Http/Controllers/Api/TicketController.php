<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Ticket::with(['user', 'unit', 'category', 'assignedAdmin'])->latest()->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'unit_id' => 'nullable|exists:units,id',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'description' => 'required|string|min:10|max:5000',
            'attachment' => 'nullable|string',
            'status' => 'nullable|in:Pending,Resolved,Rejected',
            'admin_response' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $category = TicketCategory::findOrFail($validatedData['ticket_category_id']);
        $validatedData['type'] = ucfirst($category->type);
        $validatedData['status'] = $validatedData['status'] ?? 'Pending';

        $ticket = Ticket::create($validatedData);

        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => $ticket->load(['user', 'unit', 'category', 'assignedAdmin']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        return response()->json($ticket->load(['user', 'unit', 'category', 'assignedAdmin']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validatedData = $request->validate([
            'ticket_category_id' => 'sometimes|required|exists:ticket_categories,id',
            'description' => 'sometimes|required|string|min:10|max:5000',
            'attachment' => 'nullable|string',
            'status' => 'sometimes|required|in:Pending,Resolved,Rejected',
            'admin_response' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if (isset($validatedData['ticket_category_id'])) {
            $category = TicketCategory::findOrFail($validatedData['ticket_category_id']);
            $validatedData['type'] = ucfirst($category->type);
        }

        if (isset($validatedData['status']) && $validatedData['status'] !== 'Pending') {
            $validatedData['resolved_at'] = now();
        }

        $ticket->update($validatedData);

        return response()->json([
            'message' => 'Ticket updated successfully',
            'data' => $ticket->fresh()->load(['user', 'unit', 'category', 'assignedAdmin']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted successfully',
        ]);
    }
}
