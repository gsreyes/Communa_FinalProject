<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index()
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $units = Unit::withCount(['users as active_residents_count' => function ($query) {
            $query->where('unit_users.is_active', true);
        }])
            ->with('users')
            ->orderBy('unit_number')
            ->paginate(15);

        return view('units.index', compact('units'));
    }

    public function create()
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        return view('units.create', ['unit' => null]);
    }

    public function store(Request $request)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        Unit::create($this->validatedUnit($request));

        return redirect()->route('units.index')
            ->with('success', 'Unit created successfully.');
    }

    public function show(Unit $unit)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $unit->load(['users', 'bills.billType', 'tickets.category']);

        return view('units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $unit->update($this->validatedUnit($request, $unit));

        return redirect()->route('units.show', $unit)
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        if ($unit->users()->wherePivot('is_active', true)->exists()) {
            return back()->with('error', 'Cannot delete a unit with active residents.');
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit deleted successfully.');
    }

    private function validatedUnit(Request $request, ?Unit $unit = null): array
    {
        return $request->validate([
            'unit_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'unit_number')->ignore($unit),
            ],
            'area_sqm' => ['required', 'numeric', 'min:0.01'],
        ]);
    }
}
