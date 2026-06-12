<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Display a paginated list of companies.
     *
     * Supports search by name, rfc or phone.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();

        $companies = Company::query()
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('rfc', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount('projects')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.companies.index', [
            'companies' => $companies,
            'search'    => $search->toString(),
        ]);
    }

    /**
     * Display a specific company with its projects.
     */
    public function show(Company $company): View
    {
        $company->load(['projects' => fn ($q) => $q->latest()]);

        return view('admin.companies.show', compact('company'));
    }

    /**
     * Store a newly created company in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'rfc'        => ['nullable', 'string', 'min:12', 'max:13'],
            'address'    => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'tax_regime' => ['required', Rule::in(['fisica', 'moral'])],
        ]);

        Company::create([
            'name'       => $validated['name'],
            'rfc'        => $validated['rfc'] ? strtoupper($validated['rfc']) : null,
            'address'    => $validated['address'] ?? null,
            'phone'      => $validated['phone'] ?? null,
            'tax_regime' => $validated['tax_regime'],
        ]);

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Empresa creada correctamente.');
    }

    /**
     * Update the specified company in the database.
     */
    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'rfc'        => ['nullable', 'string', 'min:12', 'max:13'],
            'address'    => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'tax_regime' => ['required', Rule::in(['fisica', 'moral'])],
        ]);

        $company->update([
            'name'       => $validated['name'],
            'rfc'        => $validated['rfc'] ? strtoupper($validated['rfc']) : null,
            'address'    => $validated['address'] ?? null,
            'phone'      => $validated['phone'] ?? null,
            'tax_regime' => $validated['tax_regime'],
        ]);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', 'Empresa actualizada correctamente.');
    }

    /**
     * Remove the specified company from the database.
     */
    public function destroy(Company $company): RedirectResponse
    {
        // Soft-delete company's projects
        $company->projects()->delete();

        // Soft-delete company
        $company->delete();

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Empresa eliminada correctamente.');
    }
}
