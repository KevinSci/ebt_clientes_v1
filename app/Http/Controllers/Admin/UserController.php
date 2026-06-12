<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a paginated list of users.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();

        $users = User::query()
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->with('companies')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', [
            'users'  => $users,
            'search' => $search->toString(),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $companies = Company::orderBy('name')->get();
        return view('admin.users.create', compact('companies'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'role'        => ['required', 'string', Rule::in(['admin', 'client'])],
            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => ['exists:companies,id'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
        ]);

        if ($user->role === 'client' && !empty($validated['company_ids'])) {
            $user->companies()->sync($validated['company_ids']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $companies = Company::orderBy('name')->get();
        $userCompanyIds = $user->companies->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'companies', 'userCompanyIds'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'role'        => ['required', 'string', Rule::in(['admin', 'client'])],
            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => ['exists:companies,id'],
        ]);

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role'  => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if ($user->role === 'client') {
            $user->companies()->sync($validated['company_ids'] ?? []);
        } else {
            // Admins don't belong to companies
            $user->companies()->detach();
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Don't allow self-deletion
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->companies()->detach();
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
