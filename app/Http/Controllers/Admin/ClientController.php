<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a paginated list of client users.
     *
     * Supports search by name or email via the `search` query parameter.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();

        $clients = User::query()
            ->where('role', 'client')
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            ->withCount('projects')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.clients.index', [
            'clients' => $clients,
            'search'  => $search->toString(),
        ]);
    }

    /**
     * Display a specific client with their projects.
     */
    public function show(User $client): View
    {
        abort_if($client->role !== 'client', 404);

        $client->load(['projects' => fn ($q) => $q->latest()]);

        return view('admin.clients.show', compact('client'));
    }

    /**
     * Store a newly created client in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:30'],
        ]);

        User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'role'         => 'client',
            'company_name' => $validated['company_name'] ?? null,
            'phone'        => $validated['phone'] ?? null,
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Update the specified client in the database.
     */
    public function update(Request $request, User $client): RedirectResponse
    {
        abort_if($client->role !== 'client', 404);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email,' . $client->id],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $updateData = [
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'company_name' => $validated['company_name'] ?? null,
            'phone'        => $validated['phone'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $client->update($updateData);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified client from the database.
     */
    public function destroy(User $client): RedirectResponse
    {
        abort_if($client->role !== 'client', 404);

        // Soft-delete client's projects
        $client->projects()->delete();

        // Soft-delete client
        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
