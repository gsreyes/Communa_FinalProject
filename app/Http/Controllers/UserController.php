<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $users = User::with('units')
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        return view('users.create', [
            'units' => Unit::orderBy('unit_number')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $validated = $this->validateUser($request);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'resident_type' => $validated['role'] === 'resident' ? $validated['resident_type'] : null,
        ]);

        $this->syncResidentUnit($user, $validated);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $user->load(['units', 'tickets', 'bills']);

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        return view('users.edit', [
            'user' => $user->load('units'),
            'units' => Unit::orderBy('unit_number')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        $validated = $this->validateUser($request, $user);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'resident_type' => $validated['role'] === 'resident' ? $validated['resident_type'] : null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $this->syncResidentUnit($user, $validated);

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_if(! Auth::user()->isAdmin(), 403);

        if ($user->is(Auth::user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'confirmed', Rules\Password::defaults()]
            : ['required', 'confirmed', Rules\Password::defaults()];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'password' => $passwordRules,
            'role' => ['required', Rule::in(['resident', 'admin', 'billing_staff'])],
            'resident_type' => ['required_if:role,resident', 'nullable', Rule::in(['owner', 'tenant'])],
            'unit_id' => ['required_if:role,resident', 'nullable', 'exists:units,id'],
        ]);
    }

    private function syncResidentUnit(User $user, array $validated): void
    {
        if ($validated['role'] !== 'resident') {
            $user->units()->detach();
            return;
        }

        $user->units()->sync([
            $validated['unit_id'] => [
                'occupant_type' => $validated['resident_type'],
                'move_in_date' => now()->toDateString(),
                'is_active' => true,
            ],
        ]);
    }
}
