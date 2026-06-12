<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Store a newly created project in the database for the given company.
     */
    public function store(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'status'              => ['required', 'string', 'in:active,paused,completed'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'created_at'          => ['nullable', 'date'],
        ]);

        $company->projects()->create([
            'name'                => $validated['name'],
            'status'              => $validated['status'],
            'progress_percentage' => $validated['progress_percentage'],
            'created_at'          => $validated['created_at'] ?? now(),
        ]);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Remove the specified project from the database.
     */
    public function destroy(Company $company, Project $project): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);

        $project->delete();

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('success', 'Proyecto eliminado correctamente.');
    }

    /**
     * Display a specific project with its posts and attachments.
     *
     * The company ownership is verified to ensure data isolation.
     */
    public function show(Company $company, Project $project): View
    {
        abort_if($project->company_id !== $company->id, 404);

        $project->load([
            'posts' => fn ($q) => $q->with('attachments')->latest('published_at'),
        ]);

        return view('admin.projects.show', compact('company', 'project'));
    }

    /**
     * Update the specified project in the database.
     */
    public function update(Request $request, Company $company, Project $project): RedirectResponse
    {
        abort_if($project->company_id !== $company->id, 404);

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'status'              => ['required', 'string', 'in:active,paused,completed'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'created_at'          => ['nullable', 'date'],
        ]);

        $project->update([
            'name'                => $validated['name'],
            'status'              => $validated['status'],
            'progress_percentage' => $validated['progress_percentage'],
            'created_at'          => $validated['created_at'] ?? $project->created_at,
        ]);

        return redirect()
            ->route('admin.companies.projects.show', [$company, $project])
            ->with('success', 'Proyecto actualizado correctamente.');
    }
}
