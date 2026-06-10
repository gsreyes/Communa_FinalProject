<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    //  Display a listing of tickets for resident or all tickets for admin
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin sees all tickets
            $tickets = Ticket::with(['user', 'unit', 'category', 'assignedAdmin'])
                ->latest()
                ->paginate(15);
        } else {
            // Resident sees only their tickets
            $tickets = $user->tickets()
                ->with(['unit', 'category', 'assignedAdmin'])
                ->latest()
                ->paginate(15);
        }

        return view('tickets.index', compact('tickets'));
    }

    // Show the form for creating a new ticket
    public function create()
    {
        $categories = TicketCategory::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $units = Auth::user()->units()
            ->wherePivot('is_active', true)
            ->get();

        return view('tickets.create', compact('categories', 'units'));
    }

    // Store a newly created ticket in database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'required|string|min:10|max:5000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $category = TicketCategory::find($validated['ticket_category_id']);
        $validated['type'] = $category->type;
        $validated['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('tickets', 'public');
            $validated['attachment'] = $path;
        }

        $ticket = Ticket::create($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket submitted successfully.');
    }

    // Display the specified ticket
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['user', 'unit', 'category', 'assignedAdmin']);

        return view('tickets.show', compact('ticket'));
    }

    // Show the form for editing the ticket (admin only)
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $categories = TicketCategory::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $admins = \App\Models\User::where('role', 'admin')
            ->where('id', '!=', Auth::id())
            ->get();

        return view('tickets.edit', compact('ticket', 'categories', 'admins'));
    }

    // Update the specified ticket (admin only)
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'status' => 'required|in:Pending,Resolved,Rejected',
            'admin_response' => 'required_if:status,Resolved,Rejected|string|min:5|max:5000',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validated['status'] !== 'Pending') {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully.');
    }

   // Delete a ticket (admin only or resident for own pending tickets)
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    // Get tickets statistics (for dashboard)
    public function getStats()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return [
                'total' => Ticket::count(),
                'pending' => Ticket::where('status', 'Pending')->count(),
                'resolved' => Ticket::where('status', 'Resolved')->count(),
                'rejected' => Ticket::where('status', 'Rejected')->count(),
            ];
        }

        return [
            'total' => $user->tickets()->count(),
            'pending' => $user->tickets()->where('status', 'Pending')->count(),
            'resolved' => $user->tickets()->where('status', 'Resolved')->count(),
            'rejected' => $user->tickets()->where('status', 'Rejected')->count(),
        ];
    }
}
