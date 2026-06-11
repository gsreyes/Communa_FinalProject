<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    //  Display a listing of tickets for resident or all tickets for admin
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        if ($user->isAdmin()) {
            // Admin sees all tickets
            $query = Ticket::with(['user', 'unit', 'category', 'assignedAdmin']);
        } else {
            // Resident sees only their tickets
            $query = $user->tickets()
                ->with(['unit', 'category', 'assignedAdmin']);
        }

        if (in_array($status, ['Pending', 'Resolved', 'Rejected'], true)) {
            $query->where('status', $status);
        }

        $tickets = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tickets.index', compact('tickets', 'status'));
    }

    // Show the form for creating a new ticket
    public function create()
    {
        $this->authorize('create', Ticket::class);

        $categories = TicketCategory::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $unit = Auth::user()->units()
            ->wherePivot('is_active', true)
            ->orderBy('units.unit_number')
            ->first();

        if (! $unit) {
            return redirect()->route('tickets.index')
                ->with('error', 'Your account must be assigned to a unit before you can submit a ticket.');
        }

        return view('tickets.create', compact('categories', 'unit'));
    }

    // Store a newly created ticket in database
    public function store(Request $request)
    {
        $this->authorize('create', Ticket::class);

        $unit = Auth::user()->units()
            ->wherePivot('is_active', true)
            ->orderBy('units.unit_number')
            ->first();

        if (! $unit) {
            return redirect()->route('tickets.index')
                ->with('error', 'Your account must be assigned to a unit before you can submit a ticket.');
        }

        $validated = $request->validate([
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'description' => 'required|string|min:10|max:5000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $category = TicketCategory::find($validated['ticket_category_id']);
        // Map lowercase category type to capitalized ticket type enum
        $validated['type'] = ucfirst($category->type);
        $validated['user_id'] = Auth::id();
        $validated['unit_id'] = $unit->id;

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
        $admins = Auth::user()->isAdmin()
            ? User::where('role', 'admin')->orderBy('name')->get()
            : collect();

        return view('tickets.show', compact('ticket', 'admins'));
    }

    // Show the form for editing the ticket (admin only)
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $categories = TicketCategory::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $admins = User::where('role', 'admin')
            ->get();

        return view('tickets.edit', compact('ticket', 'categories', 'admins'));
    }

    // Update the specified ticket (admin only)
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'status' => 'required|in:Pending,Resolved,Rejected',
            'admin_response' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validated['status'] !== 'Pending') {
            $validated['resolved_at'] = now();
        } else {
            $validated['resolved_at'] = null;
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
